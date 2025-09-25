<?php
use App\Helpers\Auth;
?>
<?php if (Auth::hayUsuarioLogueado()): ?>
<header>
    <nav>
        <h1>Hola, <?php print ucfirst(Auth::usuario())?></h1>
        <a href="/oopps/public/login/salir">Salir</a>
    </nav>    
</header>
<?php endif; ?>