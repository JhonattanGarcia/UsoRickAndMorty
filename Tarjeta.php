<?php if (isset($personaje)) : ?>
<div class="character-item">
    <div class="character-header" data-id="<?= $personaje['id'] ?>">
        <h2>#<?= $personaje['id'] ?> <?= $personaje['name'] ?>
        <span class="favorite" data-id="<?= $personaje['id'] ?>">✰</span></h2>
    </div>
    <div class="character-details" id="details-<?= $personaje['id'] ?>" style="display: none;">
        <div class="character-card">
            <img src="<?= $personaje['image'] ?>" alt="<?= $personaje['name'] ?>">
            <table>
                <tr>
                    <td><strong>Species:</strong></td>
                    <td><?= $personaje['species'] ?></td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td><?= isset($personaje['status']) ? $personaje['status'] : 'Unknown' ?></td>
                </tr>
                <tr>
                    <td><strong>Gender:</strong></td>
                    <td><?= isset($personaje['gender']) ? $personaje['gender'] : 'Unknown' ?></td>
                </tr>
                <tr>
                    <td><strong>Type:</strong></td>
                    <td><?= isset($personaje['type']) && !empty($personaje['type']) ? $personaje['type'] : 'Unknown' ?></td>
                </tr>
                <tr>
                    <td><strong>Origin:</strong></td>
                    <td><?= isset($personaje['origin']['name']) ? $personaje['origin']['name'] : 'Unknown' ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
