const formatarMoeda = (valor) =>
    valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

document.addEventListener('click', (event) => {
    const dismiss = event.target.closest('[data-dismiss]');

    if (dismiss) {
        dismiss.closest('[data-dismissible]')?.remove();
    }
});

document.addEventListener('submit', (event) => {
    const mensagem = event.target.dataset.confirm;

    if (mensagem && !window.confirm(mensagem)) {
        event.preventDefault();
    }
});

document.querySelectorAll('[data-pedido-form]').forEach((form) => {
    const lista = form.querySelector('[data-itens]');
    const taxa = form.querySelector('[data-taxa]');
    let proximoIndice = lista.querySelectorAll('[data-item]').length;

    const recalcular = () => {
        let subtotal = 0;

        lista.querySelectorAll('[data-item]').forEach((linha) => {
            const opcao = linha.querySelector('[data-produto]').selectedOptions[0];
            const preco = parseFloat(opcao?.dataset.preco ?? 0) || 0;
            const quantidade = parseInt(linha.querySelector('[data-quantidade]').value, 10) || 0;
            const totalLinha = preco * quantidade;

            subtotal += totalLinha;
            linha.querySelector('[data-linha-total]').textContent = formatarMoeda(totalLinha);
        });

        const entrega = parseFloat(taxa.value) || 0;

        form.querySelector('[data-subtotal]').textContent = formatarMoeda(subtotal);
        form.querySelector('[data-entrega]').textContent = formatarMoeda(entrega);
        form.querySelector('[data-total]').textContent = formatarMoeda(subtotal + entrega);
    };

    form.querySelector('[data-add-item]').addEventListener('click', () => {
        const modelo = lista.querySelector('[data-item]');
        const novaLinha = modelo.cloneNode(true);

        novaLinha.querySelectorAll('[name]').forEach((campo) => {
            campo.name = campo.name.replace(/itens\[\d+\]/, `itens[${proximoIndice}]`);
        });
        novaLinha.querySelector('[data-produto]').value = '';
        novaLinha.querySelector('[data-quantidade]').value = 1;
        novaLinha.querySelectorAll('.text-red-600').forEach((erro) => erro.remove());

        proximoIndice++;
        lista.appendChild(novaLinha);
        recalcular();
    });

    lista.addEventListener('click', (event) => {
        const remover = event.target.closest('[data-remove-item]');

        if (remover && lista.querySelectorAll('[data-item]').length > 1) {
            remover.closest('[data-item]').remove();
            recalcular();
        }
    });

    form.addEventListener('input', recalcular);
    form.addEventListener('change', recalcular);
    recalcular();
});
