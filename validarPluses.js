function validarPluses() {
    var pluses = document.getElementById('pluses').value;
    if(pluses === '' || isNaN(pluses) || pluses < 0 || pluses > 500) {
        alert("Por favor, introduce un valor válido para los pluses. Debe ser un número entre 0 y 500.");
        return false;
    }
    return true;
}

alert("Eres un fucking bestia Simeón");