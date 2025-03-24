document.addEventListener("DOMContentLoaded", function () {
    // Seleccionamos todos los inputs del formulario
    const inputs = document.querySelectorAll('input');
    
    // Hacemos que cada input aparezca con un pequeño retraso
    inputs.forEach((input, index) => {
        setTimeout(() => {
            input.style.opacity = '1'; // Aparece el input
            input.style.transform = 'translateX(0)'; // Vuelve a la posición original
        }, index * 300); // Retraso de 300ms por cada input
    });

    // Seleccionamos el botón
    const button = document.querySelector('button');
    
    // Hacemos que el botón aparezca después de los inputs
    setTimeout(() => {
        button.style.opacity = '1'; // Aparece el botón
        button.style.transform = 'translateX(0)'; // Vuelve a la posición original
    }, inputs.length * 300 + 500); // Retraso por todos los inputs y un extra de 500ms
});
