const express = require('express');
const routes = express.Router();
const ApiController = require('./controllers/ApiController');

//
routes.post('/criarpartida', ApiController.criarPartida);

module.exports = routes;