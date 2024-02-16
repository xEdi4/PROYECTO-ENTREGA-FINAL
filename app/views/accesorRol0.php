<table>
    <tr>
        <th style="text-align: center;">id
            <form>
                <br><button type="submit" name="ordenacion" value="id-Asc">↑</button><br>
                <button type="submit" name="ordenacion" value="id-Desc">↓</button>
            </form>
        </th>
        <th style="text-align: center;">first_name
            <form>
                <br><button type="submit" name="ordenacion" value="fname-Asc">↑</button><br>
                <button type="submit" name="ordenacion" value="fname-Desc">↓</button>
            </form>
        </th>
        <th style="text-align: center;">email
            <form>
                <br><button type="submit" name="ordenacion" value="email-Asc">↑</button><br>
                <button type="submit" name="ordenacion" value="email-Desc">↓</button>
            </form>
        </th>
        <th style="text-align: center;">gender
            <form>
                <br><button type="submit" name="ordenacion" value="gen-Asc">↑</button><br>
                <button type="submit" name="ordenacion" value="gen-Desc">↓</button>
            </form>
        </th>
        <th style="text-align: center;">ip_address
            <form>
                <br><button type="submit" name="ordenacion" value="ip-Asc">↑</button><br>
                <button type="submit" name="ordenacion" value="ip-Desc">↓</button>
            </form>
        </th>
        <th style="text-align: center;">teléfono
        </th>
    </tr>
    <?php foreach ($tvalores as $valor) : ?>
        <tr>
            <td><?= $valor->id ?> </td>
            <td><?= $valor->first_name ?> </td>
            <td><?= $valor->email ?> </td>
            <td><?= $valor->gender ?> </td>
            <td><?= $valor->ip_address ?> </td>
            <td><?= $valor->telefono ?> </td>
            <td><a href="?orden=Detalles&id=<?= $valor->id ?>">Detalles</a></td>
        <tr>
        <?php endforeach ?>
</table>

<form>
    <br>
    <button type="submit" name="nav" value="Primero">
        << </button>
            <button type="submit" name="nav" value="Anterior">
                < </button>
                    <button type="submit" name="nav" value="Siguiente"> > </button>
                    <button type="submit" name="nav" value="Ultimo"> >> </button>
</form>