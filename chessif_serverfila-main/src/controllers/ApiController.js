const axios = require('axios');

axios.defaults.baseURL = 'http://localhost:8000/api';

module.exports = {

    //x é o token
    async validarUsuario(x) {
        console.log(x);
        const response = await axios.post('/validar/token/usuario', {
            token: x,
        }).then(function (response) {
            return { 'code': response.status, 'msg': response.data.msg, 'pontuacao': response.data.pontuacao }
        }).catch(function (error) {
            console.log(error);
            return { 'code': error.response.status, 'msg': error.response.msg }
        });

        return response;

    },
    //x é o token
    async usuarioDisponivelParaFila(x) {
        const response = await axios.post('/usuario/partida/verificar', {
            token: x,
        }).then(function (response) {
            return { 'code': response.status, 'msg': response.data.msg }
        }).catch(function (error) {
            return { 'code': error.response.status, 'msg': error.response.data.msg }
        });

        return response;
    }



}