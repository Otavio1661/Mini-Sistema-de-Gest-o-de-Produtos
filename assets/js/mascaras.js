function aplicarMascaraCNPJ(valor) {
    return valor
        .replace(/\D/g, '')
        .slice(0, 14)
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
}

function aplicarMascaraTelefone(valor) {
    const digitos = valor.replace(/\D/g, '').slice(0, 11);

    if (digitos.length <= 10) {
        return digitos
            .replace(/^(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{4})(\d)/, '$1-$2');
    }

    return digitos
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d)/, '$1-$2');
}

document.querySelectorAll('#fornecedorCnpj, input[name="cnpj"]').forEach((input) => {
    input.setAttribute('maxlength', '18');
    input.addEventListener('input', () => {
        input.value = aplicarMascaraCNPJ(input.value);
    });
});

document.querySelectorAll('#fornecedorTelefone, input[name="telefone"]').forEach((input) => {
    input.setAttribute('maxlength', '15');
    input.addEventListener('input', () => {
        input.value = aplicarMascaraTelefone(input.value);
    });
});
