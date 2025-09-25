<p>Elije la oposición que quieres entrenar:</p>
<form method="post" action="/oopps/public/oposicion/comprobar">
    <label for="oposicion">Oposición: </label>
    <select name="oposicion" id="oposicion" onchange="this.form.submit()">
        <option value='default' selected>Elegir oposicion</option>
        <?php foreach ($oposiciones as $o): ?>
            <option value="<?= $o['codigo'] ?>"><?= $o['especialidad']?></option>
        <?php endforeach; ?>
    </select>
</form>