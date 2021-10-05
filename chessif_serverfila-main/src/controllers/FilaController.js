const axios = require('axios');

url = 'http://localhost:7000';

module.exports = {
    //x é o token
    async usuarioDisponivelParaFila(jogadores) {
        console.log('Aguardando resposta do servidor de jogos...');
        const response = await axios.post(url + '/criarpartida', {
            jogadores: jogadores,
        }).then(function (response) {
            //console.log(response);
            return { 'code': response.status, 'msg': response.data.msg, 'idPartida': response.data.idPartida }
        }).catch(function (error) {
            console.log('Servidor Indisponivel');
            return { 'code': 400, 'jogadores': jogadores }
        });

        return response;
    }
}