const axios = require('axios');

axios.defaults.baseURL = 'http://localhost:8000/api';

module.exports = {

    async criarPartida(req, res) {
        console.log('ALGUEM ESTÁ SOLICITANDO A CRIAÇÃO DE UM PARTIDA');
        const response = await axios.post('/criar/partida', {
            pecaBranca: req.body.jogadores.pecaBranca.id,
            pecaPreta: req.body.jogadores.pecaPreta.id,
            modo: req.body.jogadores.modo,
            secret: 'b4t4t4',
        }).then(function (response) {
            console.log(response.data);
            return { 'code': response.status, 'msg': response.data.msg, 'idPartida': response.data.idPartida }
        }).catch(function (error) {
            console.log(error);
            console.log('ERROR');
            return { 'code': error.response.status, 'msg': error.response.data.msg }
        });

        return res.status(response.code).send({
            msg: response.msg,
            idPartida: response.idPartida
        });


    },
    async enviarPartidaBd(partida) {
        const response = await axios.post('/finalizar/partida', {
            secret: 'b4t4t4',
            partida: partida
        }).then(function (response) {
            return { 'code': response.status, 'msg': response.data.msg }
        }).catch(function (error) {
            console.log(error);
            return { 'code': error.response.status, 'msg': error.response.msg }
        });

        return response;
    }


}