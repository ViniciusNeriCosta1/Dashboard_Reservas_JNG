/* =========================
   APP GLOBAL
========================= */
const App = {};

/* =========================
   HELPERS
========================= */
App.http = {
    post(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        }).then(r => r.json());
    }
};

App.toast = (msg, type = 'success') => {
    const toast = document.getElementById('toast');
    if (!toast) return;

    toast.textContent = msg;
    toast.className = `
        fixed bottom-5 right-5 px-4 py-2 rounded shadow text-white z-50
        ${type === 'error' ? 'bg-red-600' : 'bg-green-600'}
    `;
    toast.classList.remove('hidden');

    setTimeout(() => toast.classList.add('hidden'), 2500);
};

/* =========================
   MODAL
========================= */
App.Modal = {
    abrir(id) {
        const modal = document.getElementById(id);
        if (!modal) return console.error(`Modal não encontrado: ${id}`);
        modal.style.display = 'flex';
    },
    fechar(id) {
        const modal = document.getElementById(id);
        if (!modal) return console.error(`Modal não encontrado: ${id}`);
        modal.style.display = 'none';
    }
};

/* =========================
   VEÍCULOS
========================= */
App.Veiculos = {
    adicionar() {
        const nome = document.getElementById('nome_carro').value.trim();
        const display = document.getElementById('display_carro').value.trim();

        if (!nome) {
            App.toast('Informe o nome do veículo', 'error');
            return;
        }

        App.http.post('add_carro.php', {
            acao: 'add',
            nome_carro: nome,
            display_carro: display
        }).then(r => {
            r.success
                ? (App.toast('Veículo adicionado'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg || 'Erro ao adicionar', 'error');
        });
    },

    editar() {
        const select = document.getElementById('carro');
        if (!select.value) {
            App.toast('Selecione um veículo', 'error');
            return;
        }

        const opt = select.selectedOptions[0];

        document.geti = document.getElementById;
        document.getElementById('edit_carro_id').value = opt.value;
        document.getElementById('edit_nome_carro').value = opt.dataset.nome;
        document.getElementById('edit_display_carro').value = opt.dataset.display;

        App.Modal.abrir('modal-editar-carro');
    },

    salvar() {
        const id = document.getElementById('edit_carro_id').value;
        const nome = document.getElementById('edit_nome_carro').value.trim();
        const display = document.getElementById('edit_display_carro').value.trim();

        if (!nome) {
            App.toast('Nome inválido', 'error');
            return;
        }

        App.http.post('add_carro.php', {
            acao: 'edit',
            id,
            nome_carro: nome,
            display_carro: display
        }).then(r => {
            r.success
                ? (App.toast('Veículo atualizado'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg || 'Erro ao salvar', 'error');
        });
    },

    apagar() {
        const select = document.getElementById('carro');
        if (!select.value) {
            App.toast('Selecione um veículo', 'error');
            return;
        }

        if (!confirm('Deseja apagar este veículo?')) return;

        App.http.post('add_carro.php', {
            acao: 'delete',
            id: select.value
        }).then(r => {
            r.success
                ? (App.toast('Veículo removido'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg || 'Erro ao apagar', 'error');
        });
    }
};

/* =========================
   GESTORES
========================= */
App.Gestores = {
    adicionar() {
        const nome = document.getElementById('novo_gestor').value.trim();
        const email = document.getElementById('email_gestor').value.trim();

        if (!nome || !email) {
            App.toast('Preencha nome e email', 'error');
            return;
        }

        App.http.post('add_gestor.php', {
            acao: 'add',
            nome,
            email
        }).then(r => {
            r.success
                ? (App.toast('Gestor adicionado'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg || 'Erro ao adicionar', 'error');
        });
    },

    editar() {
        const select = document.getElementById('gestor');
        if (!select.value) {
            App.toast('Selecione um gestor', 'error');
            return;
        }

        const opt = select.selectedOptions[0];

        document.getElementById('edit_gestor_id').value = opt.value;
        document.getElementById('edit_nome_gestor').value = opt.dataset.nome;
        document.getElementById('edit_email_gestor').value = opt.dataset.email;

        App.Modal.abrir('modal-editar-gestor');
    },

    salvar() {
        const id = document.getElementById('edit_gestor_id').value;
        const nome = document.getElementById('edit_nome_gestor').value.trim();
        const email = document.getElementById('edit_email_gestor').value.trim();

        if (!nome || !email) {
            App.toast('Dados inválidos', 'error');
            return;
        }

        App.http.post('add_gestor.php', {
            acao: 'edit',
            id,
            nome,
            email
        }).then(r => {
            r.success
                ? (App.toast('Gestor atualizado'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg || 'Erro ao salvar', 'error');
        });
    },

    apagar() {
        const select = document.getElementById('gestor');
        if (!select.value) {
            App.toast('Selecione um gestor', 'error');
            return;
        }

        if (!confirm('Deseja apagar este gestor?')) return;

        App.http.post('add_gestor.php', {
            acao: 'delete',
            id: select.value
        }).then(r => {
            r.success
                ? (App.toast('Gestor removido'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg || 'Erro ao apagar', 'error');
        });
    }
};

/* =========================
   MANUTENÇÃO
========================= */
App.Manutencao = {

    indisponivel() {
        const id = document.getElementById('indisponivel_carro').value;
        if (!id) {
            App.toast('Selecione um veículo', 'error');
            return;
        }

        App.http.post('manutencao_carro.php', {
            acao: 'indisponivel',
            id
        }).then(r => {
            r.success
                ? (App.toast('Veículo indisponível'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg, 'error');
        });
    },

    disponivel() {
        const id = document.getElementById('indisponivel_carro').value;
        if (!id) {
            App.toast('Selecione um veículo', 'error');
            return;
        }

        App.http.post('manutencao_carro.php', {
            acao: 'disponivel',
            id
        }).then(r => {
            r.success
                ? (App.toast('Veículo disponível'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg, 'error');
        });
    },

    manutencao() {
        const id = document.getElementById('manutencao_carro').value;
        const inicio = document.getElementById('manutencao_inicio').value;
        const fim = document.getElementById('manutencao_fim').value;

        if (!id || !inicio || !fim) {
            App.toast('Preencha todos os campos', 'error');
            return;
        }

        App.http.post('manutencao_carro.php', {
            acao: 'manutencao',
            id,
            data_inicio: inicio,
            data_fim: fim
        }).then(r => {
            r.success
                ? (App.toast('Veículo em manutenção'), setTimeout(() => location.reload(), 800))
                : App.toast(r.msg, 'error');
        });
    }
};

/* =========================
   INIT
========================= */
document.addEventListener('DOMContentLoaded', () => {
    // pronto para futuros inits
});