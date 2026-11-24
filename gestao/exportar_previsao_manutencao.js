async function exportarExcel() {
    const tabela = document.querySelector('#tabelaManutencao');
    const tabelaExportacao = tabela.cloneNode(true);

    if (typeof DataTable !== 'undefined' && DataTable.isDataTable(tabela)) {
        const api = new DataTable.Api(tabela);
        const linhas = api.rows({ page: 'all', search: 'none', order: 'applied' }).nodes().toArray();
        const corpo = tabelaExportacao.tBodies[0];
        corpo.innerHTML = '';
        linhas.forEach(linha => corpo.appendChild(linha.cloneNode(true)));
    }

    const dadosMotoristas = Array.from(tabelaExportacao.tBodies[0].rows).map(linha => {
        const motorista = linha.cells[1].cloneNode(true);
        motorista.querySelectorAll('.driver-avatar, svg, i').forEach(elemento => elemento.remove());
        const texto = motorista.textContent.replace(/\s+/g, ' ').trim();
        const identificacao = texto.match(/^(.*?)\s*Mat\.\s*(\d+)\s*$/i);
        const tooltip = linha.cells[8].querySelector('[data-bs-custom-class="tooltip-observacao"]');
        const descricao = tooltip ? (tooltip.getAttribute('data-bs-original-title') || tooltip.getAttribute('title') || '') : '';
        const chamado = descricao.match(/^Chamado #(\d+)\s*—/);
        return { nome: identificacao ? identificacao[1].trim() : texto, matricula: identificacao ? identificacao[2] : '', idChamado: chamado ? chamado[1] : '' };
    });
    const matriculas = [...new Set(dadosMotoristas.map(motorista => motorista.matricula).filter(Boolean))];
    const chamados = [...new Set(dadosMotoristas.map(motorista => motorista.idChamado).filter(Boolean))];
    let hierarquia = {};
    let detalhesChamados = {};
    try {
        for (let inicio = 0; inicio < Math.max(matriculas.length, chamados.length); inicio += 500) {
            const resposta = await fetch('hierarquia_motoristas_excel.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ matriculas: matriculas.slice(inicio, inicio + 500), chamados: chamados.slice(inicio, inicio + 500) })
            });
            if (!resposta.ok) throw new Error('Não foi possível consultar supervisor e coordenador.');
            const dados = await resposta.json();
            if (!dados.success) throw new Error('Não foi possível consultar supervisor e coordenador.');
            Object.assign(hierarquia, dados.hierarquia);
            Object.assign(detalhesChamados, dados.chamados);
        }
    } catch (erro) {
        alert('Não foi possível consultar os dados para exportação. Verifique sua sessão e tente novamente.');
        return;
    }

    ['Número do chamado', 'Resposta', 'Tipo de chamado', 'Tipo de manutenção preventiva'].forEach(titulo => {
        const cabecalho = document.createElement('th');
        cabecalho.textContent = titulo;
        tabelaExportacao.tHead.rows[0].appendChild(cabecalho);
    });
    Array.from(tabelaExportacao.tBodies[0].rows).forEach(linha => {
        const tooltip = linha.cells[8].querySelector('[data-bs-custom-class="tooltip-observacao"]');
        const texto = tooltip
            ? (tooltip.getAttribute('data-bs-original-title') || tooltip.getAttribute('title') || '')
            : '';
        const chamado = texto.match(/^Chamado #(\d+)\s*—\s*([\s\S]*)$/);
        linha.insertCell().textContent = chamado ? chamado[1] : '-';
        linha.insertCell().textContent = (chamado ? chamado[2] : texto).trim() || 'Sem resposta';
        const detalhes = chamado ? (detalhesChamados[chamado[1]] || {}) : {};
        linha.insertCell().textContent = chamado ? (detalhes.tipodechamado || 'Não definido') : '-';
        linha.insertCell().textContent = chamado ? (detalhes.tipomanuprev || 'Não definido') : '-';
    });

    // Limpa apenas a copia exportada, removendo SVGs e controles da tela.
    tabelaExportacao.querySelectorAll('svg, i, button, input, select, textarea').forEach(elemento => elemento.remove());
    tabelaExportacao.querySelectorAll('th, td').forEach(celula => {
        celula.textContent = celula.textContent.replace(/\s+/g, ' ').trim();
    });
    Array.from(tabelaExportacao.tBodies[0].rows).forEach(linha => {
        [6, 9, 10].forEach(indice => {
            const celula = linha.cells[indice];
            if (celula && (celula.textContent === '' || celula.textContent === '-')) {
                celula.textContent = 'Não definido';
            }
        });
    });

    ['Matrícula', 'Supervisor', 'Coordenador'].forEach((titulo, indice) => {
        const cabecalho = document.createElement('th');
        cabecalho.textContent = titulo;
        const linha = tabelaExportacao.tHead.rows[0];
        linha.insertBefore(cabecalho, linha.cells[2 + indice]);
    });
    Array.from(tabelaExportacao.tBodies[0].rows).forEach((linha, indice) => {
        const motorista = dadosMotoristas[indice];
        const dados = hierarquia[motorista.matricula] || {};
        linha.cells[1].textContent = motorista.nome;
        [motorista.matricula, dados.supervisor || 'Não definido', dados.coordenador || 'Não definido'].forEach((texto, coluna) => {
            const celula = linha.insertCell(2 + coluna);
            celula.setAttribute('data-t', 's');
            celula.textContent = texto;
        });
    });

    const wb = XLSX.utils.table_to_book(tabelaExportacao, { sheet: 'Manutenção' });
    wb.Sheets['Manutenção']['!cols'] = [
        { wch: 14 }, // Placa
        { wch: 48 }, // Motorista
        { wch: 16 }, // Matrícula
        { wch: 40 }, // Supervisor
        { wch: 40 }, // Coordenador
        { wch: 25 }, // Centro de Custo
        { wch: 18 }, // Hodômetro
        { wch: 18 }, // Faixa Km
        { wch: 18 }, // Média Diária
        { wch: 18 }, // Data Prevista
        { wch: 24 }, // Status Manutenção
        { wch: 20 }, // Status Chamado
        { wch: 18 }, // Data Gerência
        { wch: 18 }, // Data Frota
        { wch: 20 }, // Número do chamado
        { wch: 80 }, // Resposta
        { wch: 32 }, // Tipo de chamado
        { wch: 40 }  // Tipo de manutenção preventiva
    ];
    XLSX.writeFile(wb, 'gestao_manutencao_frota.xlsx');
}