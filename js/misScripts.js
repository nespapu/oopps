// Script oposicion.php
function irAMenuOposicion () {
    let oposicion = document.getElementById("oposicion").value
    let nombre = document.getElementById("nombre").value
    document.location.href="menu.php?nombre="+nombre+"&oposicion="+oposicion
}

function irPantallaEjercicioCuantoSabesTema (pantalla, from) {
    let nombre = document.getElementById("nombre").value // este no aplica from
    let oposicion = document.getElementById("oposicion").value // este no aplica from
    let tema = (from == "input") ? document.getElementById("tema").value : getTema()  // este funciona diferente
    let dificultad = document.getElementById("dificultad").value // este no aplica from
    let numeracion = (from == "input") ? document.getElementById("numeracion").value : document.getElementById("numeracion").checked
    let apartado = (from == "input") ? document.getElementById("apartado").value : document.getElementById("apartado").checked
    let ciclos = (from == "input") ? document.getElementById("ciclos").value : document.getElementById("ciclos").checked
    let leyes = (from == "input") ? document.getElementById("leyes").value : document.getElementById("leyes").checked
    let modulos = (from == "input") ? document.getElementById("modulos").value : document.getElementById("modulos").checked
    let conceptoCita = (from == "input") ? document.getElementById("conceptoCita").value : document.getElementById("conceptoCita").checked
    let autorCita = (from == "input") ? document.getElementById("autorCita").value : document.getElementById("autorCita").checked
    let anyoCita = (from == "input") ? document.getElementById("anyoCita").value : document.getElementById("anyoCita").checked
    let cita = (from == "input") ? document.getElementById("cita").value : document.getElementById("cita").checked
    let numeracionCita = (from == "input") ? document.getElementById("numeracionCita").value : document.getElementById("numeracionCita").checked
    let apartadoCita = (from == "input") ? document.getElementById("apartadoCita").value : document.getElementById("apartadoCita").checked
    let herramienta = (from == "input") ? document.getElementById("herramienta").value : document.getElementById("herramienta").checked
    let descripcionHerramienta = (from == "input") ? document.getElementById("descripcionHerramienta").value : document.getElementById("descripcionHerramienta").checked
    let ensenyanza = (from == "input") ? document.getElementById("ensenyanza").value : document.getElementById("ensenyanza").checked
    let ciclosContexto = (from == "input") ? document.getElementById("ciclosContexto").value : document.getElementById("ciclosContexto").checked
    let modulosContexto = (from == "input") ? document.getElementById("modulosContexto").value : document.getElementById("modulosContexto").checked
    let conceptoContextoEscolar = (from == "input") ? document.getElementById("conceptoContextoEscolar").value : document.getElementById("conceptoContextoEscolar").checked
    let aplicacionContextoEscolar = (from == "input") ? document.getElementById("aplicacionContextoEscolar").value : document.getElementById("aplicacionContextoEscolar").checked
    let metodo = (from == "input") ? document.getElementById("metodo").value : document.getElementById("metodo").checked
    let campo = (from == "input") ? document.getElementById("campo").value : document.getElementById("campo").checked
    let profesional = (from == "input") ? document.getElementById("profesional").value : document.getElementById("profesional").checked
    let conceptoContextoLaboral = (from == "input") ? document.getElementById("conceptoContextoLaboral").value : document.getElementById("conceptoContextoLaboral").checked
    let aplicacionContextoLaboral = (from == "input") ? document.getElementById("aplicacionContextoLaboral").value : document.getElementById("aplicacionContextoLaboral").checked
    let beneficio = (from == "input") ? document.getElementById("beneficio").value : document.getElementById("beneficio").checked
    let autorLibro = (from == "input") ? document.getElementById("autorLibro").value : document.getElementById("autorLibro").checked
    let anyoLibro = (from == "input") ? document.getElementById("anyoLibro").value : document.getElementById("anyoLibro").checked
    let tituloLibro = (from == "input") ? document.getElementById("tituloLibro").value : document.getElementById("tituloLibro").checked
    let editorial = (from == "input") ? document.getElementById("editorial").value : document.getElementById("editorial").checked
    let nombreWeb = (from == "input") ? document.getElementById("nombreWeb").value : document.getElementById("nombreWeb").checked
    let url = (from == "input") ? document.getElementById("url").value : document.getElementById("url").checked
    document.location.href = pantalla + "?nombre=" + nombre + "&oposicion=" + oposicion 
    + "&tema=" + tema + "&dificultad=" + dificultad
    + "&numeracion=" + numeracion + "&apartado=" + apartado
    + "&ciclos=" + ciclos + "&leyes=" + leyes + "&modulos=" + modulos 
    + "&conceptoCita=" + conceptoCita + "&autorCita=" + autorCita + "&anyoCita=" + anyoCita + "&cita=" + cita + "&numeracionCita=" + numeracionCita + "&apartadoCita=" + apartadoCita 
    + "&herramienta=" + herramienta + "&descripcionHerramienta=" + descripcionHerramienta
    + "&ensenyanza=" + ensenyanza + "&ciclosContexto=" + ciclosContexto + "&modulosContexto=" + modulosContexto + "&conceptoContextoEscolar=" + conceptoContextoEscolar + "&aplicacionContextoEscolar=" + aplicacionContextoEscolar + "&metodo=" + metodo 
    + "&campo=" + campo + "&profesional=" + profesional + "&conceptoContextoLaboral=" + conceptoContextoLaboral + "&aplicacionContextoLaboral=" + aplicacionContextoLaboral + "&beneficio=" + beneficio 
    + "&autorLibro=" + autorLibro + "&anyoLibro=" + anyoLibro + "&tituloLibro=" + tituloLibro + "&editorial=" + editorial 
    + "&nombreWeb=" + nombreWeb + "&url=" + url
}

function getTema () {
    let select = document.getElementById("tema")
    let tema = select.value
    if(select.value == 0){
        tema = getTemaAleatorio(select.getElementsByTagName("option"))
    }
    return tema
}

function getTemaAleatorio (opciones) {
    let numeroAleatorio = Math.floor(Math.random() * (opciones.length - 2) + 2); //No contar opción por defecto ni opción aleatoria
    return opciones[numeroAleatorio].value
}

// Script indice.php
function corregirEjercicioIndice () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarNumeracion = document.getElementById("numeracion").value
    let mostrarApartado = document.getElementById("apartado").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarNumeracion == "false"){
            corregir(contenedores_pregunta_solucion[i], "Numeracion", "igualdad")
        }
        if(mostrarApartado == "false"){
            corregir(contenedores_pregunta_solucion[i], "Apartado", "igualdad")
        }
    }
}


function reiniciarEjercicioIndice () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarNumeracion = document.getElementById("numeracion").value
    let mostrarApartado = document.getElementById("apartado").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if(mostrarNumeracion == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Numeracion")
        }
        if(mostrarApartado == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Apartado")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioIndice () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionNumeracion = contenedores_pregunta_solucion[i].getElementsByClassName("solucionNumeracion")[0]
        let solucionApartado = contenedores_pregunta_solucion[i].getElementsByClassName("solucionApartado")[0]
        let span = document.createElement("span");
        span.innerHTML = solucionNumeracion.value + ". " + solucionApartado.value
        contenedores_pregunta_solucion[i].getElementsByClassName("contenedor_solucion")[0].appendChild(span)
    }
}

// Script justificacion.php
function corregirEjercicioJustificacion () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarCiclos = document.getElementById("ciclos").value
    let mostrarLeyes = document.getElementById("leyes").value
    let mostrarModulos = document.getElementById("modulos").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarCiclos == "false"){
            corregir(contenedores_pregunta_solucion[i], "Ciclo", "igualdad")
        }
        if (mostrarLeyes == "false"){
            corregir(contenedores_pregunta_solucion[i], "Ley", "igualdad")
        }
        if (mostrarModulos == "false"){
            corregir(contenedores_pregunta_solucion[i], "Modulo", "igualdad")
        }
    }
}

function reiniciarEjercicioJustificacion () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarCiclos = document.getElementById("ciclos").value
    let mostrarLeyes = document.getElementById("leyes").value
    let mostrarModulos = document.getElementById("modulos").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if(mostrarCiclos == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Ciclo")
        }
        if(mostrarLeyes == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Ley")
        }
        if(mostrarModulos == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Modulo")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioJustificacion () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionCiclo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionCiclo")[0]
        let solucionLeyes = contenedores_pregunta_solucion[i].getElementsByClassName("solucionLey")
        let solucionModulos = contenedores_pregunta_solucion[i].getElementsByClassName("solucionModulo")
        let ciclo = document.createElement("h3");
        ciclo.innerHTML = solucionCiclo.value
        let cabeceraLeyes = document.createElement("h4")
        cabeceraLeyes.innerHTML = "Leyes"
        let listaLeyes = document.createElement("ul")
        for(let i=0; i<solucionLeyes.length; i++){
            let listaLeyesItem = document.createElement("li")
            listaLeyesItem.innerHTML = solucionLeyes[i].value
            listaLeyes.appendChild(listaLeyesItem)
        }
        let cabeceraModulos = document.createElement("h4")
        cabeceraModulos.innerHTML = "Módulos"
        let listaModulos = document.createElement("ul")
        for(let i=0; i<solucionModulos.length; i++){
            let listaModulosItem = document.createElement("li")
            listaModulosItem.innerHTML = solucionModulos[i].value
            listaModulos.appendChild(listaModulosItem)
        }
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(ciclo)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(listaLeyes)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(listaModulos)
    }
}

// Script citas.php
function corregirEjercicioCitas () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarConceptoCita = document.getElementById("conceptoCita").value
    let mostrarAutorCita = document.getElementById("autorCita").value
    let mostrarAnyoCita = document.getElementById("anyoCita").value
    let mostraNumeracionCita = document.getElementById("numeracionCita").value
    let mostrarApartadoCita = document.getElementById("apartadoCita").value
    let mostrarCita = document.getElementById("cita").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarConceptoCita == "false"){
            corregir(contenedores_pregunta_solucion[i], "Concepto", "igualdad")
        }
        if (mostrarAutorCita == "false"){
            corregir(contenedores_pregunta_solucion[i], "Autor", "igualdad")
        }
        if (mostrarAnyoCita == "false"){
            corregir(contenedores_pregunta_solucion[i], "Anyo", "igualdad")
        }
        if (mostraNumeracionCita == "false"){
            corregir(contenedores_pregunta_solucion[i], "Numeracion", "igualdad")
        }
        if (mostrarApartadoCita == "false"){
            corregir(contenedores_pregunta_solucion[i], "Apartado", "igualdad")
        }
        if (mostrarCita == "false"){
            corregir(contenedores_pregunta_solucion[i], "Cita", "similitud")
        }
    }
}

function reiniciarEjercicioCitas () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarConceptoCita = document.getElementById("conceptoCita").value
    let mostrarAutorCita = document.getElementById("autorCita").value
    let mostrarAnyoCita = document.getElementById("anyoCita").value
    let mostraNumeracionCita = document.getElementById("numeracionCita").value
    let mostrarApartadoCita = document.getElementById("apartadoCita").value
    let mostrarCita = document.getElementById("cita").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarConceptoCita == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Concepto")
        }
        if (mostrarAutorCita == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Autor")
        }
        if (mostrarAnyoCita == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Anyo")
        }
        if (mostraNumeracionCita == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Numeracion")
        }
        if (mostrarApartadoCita == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Apartado")
        }
        if (mostrarCita == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Cita")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioCitas () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionConcepto = contenedores_pregunta_solucion[i].getElementsByClassName("solucionConcepto")[0]
        let solucionAutor = contenedores_pregunta_solucion[i].getElementsByClassName("solucionAutor")[0]
        let solucionAnyo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionAnyo")[0]
        let solucionNumeracion = contenedores_pregunta_solucion[i].getElementsByClassName("solucionNumeracion")[0]
        let solucionApartado = contenedores_pregunta_solucion[i].getElementsByClassName("solucionApartado")[0]
        let solucionCita = contenedores_pregunta_solucion[i].getElementsByClassName("solucionCita")[0]
        let concepto = document.createElement("span");
        concepto.innerHTML = solucionConcepto.value
        let autor = document.createElement("span")
        autor.innerHTML = "(" + solucionAutor.value + ","
        let anyo = document.createElement("span")
        anyo.innerHTML = solucionAnyo.value + ")"
        let br = document.createElement("br")
        let numeracion = document.createElement("span")
        numeracion.innerHTML = solucionNumeracion.value + ". "
        let apartado = document.createElement("span")
        apartado.innerHTML = solucionApartado.value
        let cita = document.createElement("p")
        cita.innerHTML = solucionCita.value
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(concepto)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(autor)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(anyo)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(br)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(numeracion)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(apartado)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(cita)
    }
}

// Script herramientas.php
function corregirEjercicioHerramientas () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarNombre = document.getElementById("herramienta").value
    let mostrarDescripcion = document.getElementById("descripcionHerramienta").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarNombre == "false"){
            corregir(contenedores_pregunta_solucion[i], "Nombre", "igualdad")
        }
        if (mostrarDescripcion == "false"){
            corregir(contenedores_pregunta_solucion[i], "Descripcion", "similitud")
        }
    }
}

function reiniciarEjercicioHerramientas () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarNombre = document.getElementById("herramienta").value
    let mostrarDescripcion = document.getElementById("descripcionHerramienta").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarNombre == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Nombre")
        }
        if (mostrarDescripcion == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Descripcion")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioHerramientas () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionNombre = contenedores_pregunta_solucion[i].getElementsByClassName("solucionNombre")[0]
        let solucionDescripcion = contenedores_pregunta_solucion[i].getElementsByClassName("solucionDescripcion")[0]
        let nombre = document.createElement("span");
        nombre.innerHTML = solucionNombre.value
        let descripcion = document.createElement("p")
        descripcion.innerHTML = solucionDescripcion.value
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(nombre)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(descripcion)
    }
}

// Script contextoEscolar.php
function corregirEjercicioContextoEscolar () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarEnsenyanza = document.getElementById("ensenyanza").value
    let mostrarCiclos = document.getElementById("ciclosContexto").value
    let mostrarModulos = document.getElementById("modulosContexto").value
    let mostrarConcepto = document.getElementById("conceptoContextoEscolar").value
    let mostrarAplicación = document.getElementById("aplicacionContextoEscolar").value
    let mostrarMetodo = document.getElementById("metodo").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarEnsenyanza == "false"){
            corregir(contenedores_pregunta_solucion[i], "Ensenyanza", "igualdad")
        }
        if (mostrarCiclos == "false"){
            corregir(contenedores_pregunta_solucion[i], "Ciclos", "igualdad")
        }
        if (mostrarModulos == "false"){
            corregir(contenedores_pregunta_solucion[i], "Modulos", "igualdad")
        }
        if (mostrarConcepto == "false"){
            corregir(contenedores_pregunta_solucion[i], "Concepto", "similitud")
        }
        if (mostrarAplicación == "false"){
            corregir(contenedores_pregunta_solucion[i], "Aplicacion", "similitud")
        }
        if (mostrarMetodo == "false"){
            corregir(contenedores_pregunta_solucion[i], "Metodo", "similitud")
        }
    }
}

function reiniciarEjercicioContextoEscolar () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarEnsenyanza = document.getElementById("ensenyanza").value
    let mostrarCiclos = document.getElementById("ciclosContexto").value
    let mostrarModulos = document.getElementById("modulosContexto").value
    let mostrarConcepto = document.getElementById("conceptoContextoEscolar").value
    let mostrarAplicación = document.getElementById("aplicacionContextoEscolar").value
    let mostrarMetodo = document.getElementById("metodo").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarEnsenyanza == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Ensenyanza")
        }
        if (mostrarCiclos == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Ciclos")
        }
        if (mostrarModulos == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Modulos")
        }
        if (mostrarConcepto == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Concepto")
        }
        if (mostrarAplicación == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Aplicacion")
        }
        if (mostrarMetodo == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Metodo")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioContextoEscolar () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionEnsenyanza = contenedores_pregunta_solucion[i].getElementsByClassName("solucionEnsenyanza")[0]
        let solucionCiclos = contenedores_pregunta_solucion[i].getElementsByClassName("solucionCiclos")[0]
        let solucionModulos = contenedores_pregunta_solucion[i].getElementsByClassName("solucionModulos")[0]
        let solucionConcepto = contenedores_pregunta_solucion[i].getElementsByClassName("solucionConcepto")[0]
        let solucionAplicacion = contenedores_pregunta_solucion[i].getElementsByClassName("solucionAplicacion")[0]
        let solucionMetodo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionMetodo")[0]
        let ensenyanza = document.createElement("p");
        ensenyanza.innerHTML = solucionEnsenyanza.value
        let ciclos = document.createElement("p");
        ciclos.innerHTML = solucionCiclos.value
        let modulos = document.createElement("p");
        modulos.innerHTML = solucionModulos.value
        let concepto = document.createElement("p")
        concepto.innerHTML = solucionConcepto.value
        let aplicacion = document.createElement("p")
        aplicacion.innerHTML = solucionAplicacion.value
        let metodo = document.createElement("p")
        metodo.innerHTML = solucionMetodo.value
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(ensenyanza)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(ciclos)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(modulos)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(concepto)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(aplicacion)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(metodo)
    }
}

// Script contextoEscolar.php
function corregirEjercicioContextoLaboral () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarCampo = document.getElementById("campo").value
    let mostrarProfesional = document.getElementById("profesional").value
    let mostrarConcepto = document.getElementById("conceptoContextoLaboral").value
    let mostrarAplicación = document.getElementById("aplicacionContextoLaboral").value
    let mostrarBeneficio = document.getElementById("beneficio").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarCampo == "false"){
            corregir(contenedores_pregunta_solucion[i], "Campo", "igualdad")
        }
        if (mostrarProfesional == "false"){
            corregir(contenedores_pregunta_solucion[i], "Profesional", "igualdad")
        }
        if (mostrarConcepto == "false"){
            corregir(contenedores_pregunta_solucion[i], "Concepto", "similitud")
        }
        if (mostrarAplicación == "false"){
            corregir(contenedores_pregunta_solucion[i], "Aplicacion", "similitud")
        }
        if (mostrarBeneficio == "false"){
            corregir(contenedores_pregunta_solucion[i], "Beneficio", "similitud")
        }
    }
}

function reiniciarEjercicioContextoLaboral () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarCampo = document.getElementById("campo").value
    let mostrarProfesional = document.getElementById("profesional").value
    let mostrarConcepto = document.getElementById("conceptoContextoLaboral").value
    let mostrarAplicación = document.getElementById("aplicacionContextoLaboral").value
    let mostrarBeneficio = document.getElementById("beneficio").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarCampo == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Campo")
        }
        if (mostrarProfesional == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Profesional")
        }
        if (mostrarConcepto == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Concepto")
        }
        if (mostrarAplicación == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Aplicacion")
        }
        if (mostrarBeneficio == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Beneficio")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioContextoLaboral () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionCampo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionCampo")[0]
        let solucionProfesional = contenedores_pregunta_solucion[i].getElementsByClassName("solucionProfesional")[0]
        let solucionConcepto = contenedores_pregunta_solucion[i].getElementsByClassName("solucionConcepto")[0]
        let solucionAplicacion = contenedores_pregunta_solucion[i].getElementsByClassName("solucionAplicacion")[0]
        let solucionBeneficio = contenedores_pregunta_solucion[i].getElementsByClassName("solucionBeneficio")[0]
        let campo = document.createElement("span");
        campo.innerHTML = solucionCampo.value
        let profesional = document.createElement("span");
        profesional.innerHTML = solucionProfesional.value
        let concepto = document.createElement("p")
        concepto.innerHTML = solucionConcepto.value
        let aplicacion = document.createElement("p")
        aplicacion.innerHTML = solucionAplicacion.value
        let beneficio = document.createElement("p")
        beneficio.innerHTML = solucionBeneficio.value
        let br = document.createElement("br")
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(campo)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(br)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(profesional)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(concepto)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(aplicacion)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(beneficio)
    }
}

// Script bibliografia.php
function corregirEjercicioBibliografia () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarAutor = document.getElementById("autorLibro").value
    let mostrarAnyo = document.getElementById("anyoLibro").value
    let mostrarTitulo = document.getElementById("tituloLibro").value
    let mostrarEditorial = document.getElementById("editorial").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarAutor == "false"){
            corregir(contenedores_pregunta_solucion[i], "Autor", "igualdad")
        }
        if (mostrarAnyo == "false"){
            corregir(contenedores_pregunta_solucion[i], "Anyo", "igualdad")
        }
        if (mostrarTitulo == "false"){
            corregir(contenedores_pregunta_solucion[i], "Titulo", "igualdad")
        }
        if (mostrarEditorial == "false"){
            corregir(contenedores_pregunta_solucion[i], "Editorial", "igualdad")
        }
    }
}

function reiniciarEjercicioBibliografia () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarAutor = document.getElementById("autorLibro").value
    let mostrarAnyo = document.getElementById("anyoLibro").value
    let mostrarTitulo = document.getElementById("tituloLibro").value
    let mostrarEditorial = document.getElementById("editorial").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarAutor == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Autor")
        }
        if (mostrarAnyo == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Anyo")
        }
        if (mostrarTitulo == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Titulo")
        }
        if (mostrarEditorial == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Editorial")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioBibliografia () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionAutor = contenedores_pregunta_solucion[i].getElementsByClassName("solucionAutor")[0]
        let solucionAnyo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionAnyo")[0]
        let solucionTitulo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionTitulo")[0]
        let solucionEditorial = contenedores_pregunta_solucion[i].getElementsByClassName("solucionEditorial")[0]
        let autor = document.createElement("span");
        autor.innerHTML = solucionAutor.value
        let anyo = document.createElement("span")
        anyo.innerHTML = "(" + solucionAnyo.value  + ")";
        let titulo = document.createElement("span")
        titulo.innerHTML = solucionTitulo.value
        let editorial = document.createElement("span")
        editorial.innerHTML = "(" + solucionEditorial.value + ")"
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(autor)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(anyo)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(titulo)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(editorial)
    }
}

// Script bibliografia.php
function corregirEjercicioWebgrafia () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarNombre = document.getElementById("nombreWeb").value
    let mostrarUrl = document.getElementById("url").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarNombre == "false"){
            corregir(contenedores_pregunta_solucion[i], "Nombre", "igualdad")
        }
        if (mostrarUrl == "false"){
            corregir(contenedores_pregunta_solucion[i], "Url", "igualdad")
        }
    }
}

function reiniciarEjercicioWebgrafia () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    let mostrarNombre = document.getElementById("nombreWeb").value
    let mostrarUrl = document.getElementById("url").value
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        if (mostrarNombre == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Nombre")
        }
        if (mostrarUrl == "false"){
            reiniciar(contenedores_pregunta_solucion[i], "Url")
        }
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioWebgrafia () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionNombre = contenedores_pregunta_solucion[i].getElementsByClassName("solucionNombre")[0]
        let solucionUrl = contenedores_pregunta_solucion[i].getElementsByClassName("solucionUrl")[0]
        let nombre = document.createElement("span");
        nombre.innerHTML = solucionNombre.value
        let url = document.createElement("span")
        url.innerHTML = "(" + solucionUrl.value  + ")";
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(nombre)
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(url)
    }
}

// Script titulo.php
function corregirEjercicioTitulo () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        corregir(contenedores_pregunta_solucion[i], "Titulo", "igualdad")
    }
}

function reiniciarEjercicioTitulo () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        reiniciar(contenedores_pregunta_solucion[i], "Titulo")
        quitarSolucion(contenedores_pregunta_solucion[i])
    }
}

function mostrarSolucionEjercicioTitulo () {
    let contenedores_pregunta_solucion = document.getElementsByClassName("contenedor_pregunta_solucion")
    for(let i=0; i<contenedores_pregunta_solucion.length; i++){
        let solucionTitulo = contenedores_pregunta_solucion[i].getElementsByClassName("solucionTitulo")[0]
        let titulo = document.createElement("span");
        titulo.innerHTML = solucionTitulo.value
        contenedores_pregunta_solucion[i].getElementsByClassName("solucion_visible")[0].appendChild(titulo)
    }
}

function corregir (contenedor_pregunta_solucion, seccion, modo) {
    let rellenar = contenedor_pregunta_solucion.getElementsByClassName("rellenar"+seccion)
    let solucion = contenedor_pregunta_solucion.getElementsByClassName("solucion"+seccion)
    for (let i=0; i<rellenar.length; i++){
        if(modo == "igualdad"){
            (rellenar[i].value.toLowerCase() != solucion[i].value.toLowerCase()) ? rellenar[i].classList.add("incorrecto") : rellenar[i].classList.add("correcto")
        }else {
            let similitud = stringSimilarity.compareTwoStrings(rellenar[i].value.toLowerCase(), solucion[i].value.toLowerCase()) 
            console.log("Similitud: " + similitud)
            similitud < 0.7 ? rellenar[i].classList.add("incorrecto") : rellenar[i].classList.add("correcto")
        }
    }
}

function reiniciar (contenedor_pregunta_solucion, seccion) {
    let rellenar = contenedor_pregunta_solucion.getElementsByClassName("rellenar"+seccion)
    for(let i=0; i<rellenar.length; i++){
        rellenar[i].value=""
        rellenar[i].classList.remove("incorrecto")
        rellenar[i].classList.remove("correcto")
    }
}

function quitarSolucion(contenedor_pregunta_solucion) {
    // Almacenar los inputs con las soluciones para volver a definirlos tras borrar la solucion visible
    if(contenedor_pregunta_solucion.getElementsByClassName("solucion_visible")[0].hasChildNodes()){
        contenedor_pregunta_solucion.getElementsByClassName("solucion_visible")[0].replaceChildren()
    }
    
}

function habilitarBotonContinuar () {
    let numInputRellenar = document.getElementsByClassName("rellenar").length
    let numInputSolucionCorrecta = document.getElementsByClassName("correcto").length
    document.getElementById("boton_continuar").disabled = numInputRellenar != numInputSolucionCorrecta
}

function irPantallaEjercicioSimulacro(){
    let oposicion = document.getElementById("oposicion").value
    let nombre = document.getElementById("nombre").value
    document.location.href = "simulacroExamen.php?nombre="+nombre+"&oposicion=" + oposicion
}