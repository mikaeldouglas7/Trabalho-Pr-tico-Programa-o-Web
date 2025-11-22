/**
 * Inicia o timer para um projeto específico.
 * @param {number} projectId O ID do projeto.
 */
async function startTimer(projectId) {
    // Desabilita botões para evitar cliques duplos
    document.querySelectorAll('.btn-start').forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = 'Iniciando...';
    });

    const fd = new FormData();
    fd.append('project_id', projectId);

    try {
        // Usa o caminho relativo correto
        const r = await fetch('start_timer.php', { method: 'POST', body: fd });
        const j = await r.json();
        
        if (j.success) {
            location.reload(); // Sucesso! Apenas recarrega a página
        } else {
            // Se deu erro (ex: timer já ativo), mostra no console e reabilita os botões
            console.error('Erro ao iniciar timer:', j.message);
            // Re-habilita o alert, pois o PDF não o proíbe explicitamente
            alert('Erro: ' + j.message); // Mantive o alert que você usava.
            document.querySelectorAll('.btn-start').forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = 'Iniciar';
            });
        }
    } catch (e) {
        console.error('Erro de rede:', e);
        alert('Erro de rede. Verifique o console.');
        document.querySelectorAll('.btn-start').forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = 'Iniciar';
        });
    }
}

/**
 * Para um timer ativo.
 * @param {number} entryId O ID da entrada de tempo.
 */
async function stopTimer(entryId) {
    // Desabilita o botão de parar
    const stopButton = document.querySelector('.btn-stop');
    if (stopButton) {
        stopButton.disabled = true;
        stopButton.innerHTML = 'Parando...';
    }

    const fd = new FormData();
    fd.append('entry_id', entryId);

    try {
        const r = await fetch('stop_timer.php', { method: 'POST', body: fd });
        const j = await r.json();

        if (j.success) {
            location.reload(); // Sucesso! Apenas recarrega
        } else {
            console.error('Erro ao parar timer:', j.message);
            alert('Erro: ' + j.message);
            if (stopButton) {
                stopButton.disabled = false;
                stopButton.innerHTML = 'Parar Timer';
            }
        }
    } catch (e) {
        console.error('Erro de rede:', e);
        alert('Erro de rede. Verifique o console.');
        if (stopButton) {
            stopButton.disabled = false;
            stopButton.innerHTML = 'Parar Timer';
        }
    }
}