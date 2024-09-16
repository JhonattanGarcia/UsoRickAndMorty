import requests
import pymysql  
import time

# Conexión a la base de datos
connection = pymysql.connect(
    host='localhost',  
    user='root',
    password='',       
    database='bd_rick&morty'
)

def insert_location(cursor, location):
    query = """
    INSERT INTO locations (id, name, type, dimension, url, created_at)
    VALUES (%s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE name=VALUES(name);
    """
    cursor.execute(query, (
        location['id'], 
        location['name'], 
        location['type'], 
        location['dimension'], 
        location['url'], 
        location['created']
    ))

def insert_character(cursor, character):
    query = """
    INSERT INTO characters (id, name, status, species, type, gender, origin_location_id, current_location_id, image, url, created_at)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE name=VALUES(name);
    """
    
    # Origen y localización actual pueden ser None
    origin_location_id = character['origin']['url'].split('/')[-1] if character['origin']['url'] else None
    current_location_id = character['location']['url'].split('/')[-1] if character['location']['url'] else None

    cursor.execute(query, (
        character['id'], 
        character['name'], 
        character['status'], 
        character['species'], 
        character['type'], 
        character['gender'], 
        origin_location_id, 
        current_location_id, 
        character['image'], 
        character['url'], 
        character['created']
    ))

def insert_episode(cursor, episode):
    query = """
    INSERT INTO episodes (id, name, air_date, episode_code, url, created_at)
    VALUES (%s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE name=VALUES(name);
    """
    cursor.execute(query, (
        episode['id'], 
        episode['name'], 
        episode['air_date'], 
        episode['episode'], 
        episode['url'], 
        episode['created']
    ))

def insert_character_episode(cursor, character_id, episode_id):
    query = """
    INSERT INTO characters_episodes (character_id, episode_id)
    VALUES (%s, %s)
    ON DUPLICATE KEY UPDATE character_id=VALUES(character_id);
    """
    cursor.execute(query, (character_id, episode_id))

# Función para obtener y llenar personajes
def fetch_and_fill_characters():
    url = 'https://rickandmortyapi.com/api/character'
    
    while url:
        response = requests.get(url)
        data = response.json()

        with connection.cursor() as cursor:
            for character in data['results']:
                # Inserta el personaje
                insert_character(cursor, character)
                # Inserta el origen y localización actual si existen
                if character['origin']['url']:
                    origin_id = character['origin']['url'].split('/')[-1]
                    origin = requests.get(character['origin']['url']).json()
                    insert_location(cursor, origin)

                if character['location']['url']:
                    location_id = character['location']['url'].split('/')[-1]
                    location = requests.get(character['location']['url']).json()
                    insert_location(cursor, location)
                    
            connection.commit()

        # Verifica si hay más páginas
        url = data['info']['next']
        time.sleep(1)  # Pausa para no hacer demasiadas solicitudes a la API rápidamente

# Función para obtener y llenar episodios
def fetch_and_fill_episodes():
    url = 'https://rickandmortyapi.com/api/episode'
    
    while url:
        response = requests.get(url)
        data = response.json()

        with connection.cursor() as cursor:
            for episode in data['results']:
                # Inserta el episodio
                insert_episode(cursor, episode)
                # Relacionar los personajes con el episodio
                for character_url in episode['characters']:
                    character_id = character_url.split('/')[-1]
                    insert_character_episode(cursor, character_id, episode['id'])
                    
            connection.commit()

        # Verifica si hay más páginas
        url = data['info']['next']
        time.sleep(1)

if __name__ == '__main__':
    try:
        # Llenar personajes y sus ubicaciones
        fetch_and_fill_characters()
        # Llenar episodios y las relaciones con los personajes
        fetch_and_fill_episodes()
    finally:
        connection.close()
