const app = require('express');
const server = require('http').createServer(app);
const io = require('socket.io')(server);
const CryptoJS = require("crypto-js");
const Base64 = require('js-base64');
const os = require("os");
var osu = require('node-os-utils')
var cpu = osu.cpu


const ApiController = require('./src/controllers/ApiController');
const FilaController = require('./src/controllers/FilaController');
const { Console } = require('console');

let filaBronze = { jogador: [] }; // Bronze entre 0 a 499 pontos
let filaPrata = { jogador: [] }; //  Prata entre 500 a 699 pontos 
let filaOuro = { jogador: [] }; //  Ouro entre 700 a 899 pontos 
let filaPlatina = { jogador: [] };  //  Platina entre 900 a 1099 pontos 
let filaDiamante = { jogador: [] };  //  Diamante entre 1100 a 1399 pontos 
let filaMestre = { jogador: [] }; //  Mestre entre 1400 a 1799 pontos 
let filaDesafiante = { jogador: [] }; // Desafiante acima de 1800 pontos
let filaCasual = { jogador: [] };
let idJogadoresFila = [];
let jogadoresOnline = { jogador: [] }
let idJogadoresSendoDesafiado = [];

async function enviarParaPartida(jogador1, jogador2, modo) {
    var jogadorBranco = (Math.random() >= 0.5) ? 1 : 0;

    if (jogadorBranco) {
        var pecaBranca = jogador1;
        var pecaPreta = jogador2;
    } else {
        var pecaBranca = jogador2;
        var pecaPreta = jogador1;
    }

    var obj = { "pecaBranca": pecaBranca, "pecaPreta": pecaPreta, "modo": modo };

    console.log('Enviando para servidor de jogos...');
    //console.log(obj);
    return res = await FilaController.usuarioDisponivelParaFila(obj);
}

async function buscarOponente(fila, usuario, modo) {
    //Tem jogadores na fila
    if (modo == undefined) {
        modo = 'ranqueado';
    }
    if (Object.keys(fila.jogador).length > 0) {
        console.log('FILA TEM MAIS DE ' + fila.jogador.length + ' JOGADOR');
        var indexJogador = parseInt(Math.random() * fila.jogador.length);
        var jogadorSelecionado = fila.jogador[indexJogador];
        fila.jogador.splice(jogadorSelecionado);
        var resposta = await enviarParaPartida(usuario, jogadorSelecionado, modo);
        return [resposta, usuario, jogadorSelecionado];
    } else {
        // Não tem jogadores na fila
        console.log('ADICIONANDO USUARIO NA FILA');
        fila.jogador.push(usuario);
        console.log(fila);
        return { 'usuarioNaFila': true };
    }

}

function cpuAverage() {


    var totalIdle = 0, totalTick = 0;
    var cpus = os.cpus();


    for (var i = 0, len = cpus.length; i < len; i++) {


        var cpu = cpus[i];


        for (type in cpu.times) {
            totalTick += cpu.times[type];
        }


        totalIdle += cpu.times.idle;
    }


    return { idle: totalIdle / cpus.length, total: totalTick / cpus.length };
}


var startMeasure = cpuAverage();

io.on('connection', socket => {
    console.log(`Usuário conectado socketID: ${socket.id}`);
    socket.entrouNaFila = false;

    socket.on('desclararId', async (data) => {
        socket.idJogador = data;
    });

    socket.on('getConsumo', async (data) => {
        var memoriaFree = (os.freemem() / 1000 / 1000).toFixed(0);
        var memoriaTotal = (os.totalmem() / 1000 / 1000).toFixed(0);
        //Output result to console

        percentageCPU = await cpu.usage()
            .then(info => {
                return info;
            });



        return socket.emit('getConsumo', { 'cpu': percentageCPU, 'mFree': memoriaFree, 'mUse': memoriaTotal, 'usuarios': Object.keys(io.sockets.sockets).length });
    });



    socket.on('entrarNaFilaCasual', async (data) => {

        const usuario = JSON.parse(data);
        usuario.socket = socket.id;
        socket.usuario = usuario;
        socket.idJogador = usuario.id;
        socket.entrouNaFila = true;

        console.log('Usuário solicitando para entrar na fila CASUAL.. || id:' + usuario.id);
        if (usuario.id == null) {
            //VAI TOMAR DISCONECT DO socket.IO
            console.log('Solicitação para entrar na fila  CASUAL negada, motivo: sem id');
        } else {
            if (idJogadoresFila.some(elem => elem == usuario.id)) {
                console.log("Usuário ja na fila");
                return socket.emit('comunicacao', { 'msg': 'Usuario ja esta na fila' });
            } else {
                idJogadoresFila.push(usuario.id);

                console.log("FILA CASUAL");
                usuario.fila = filaCasual;
                const res = await buscarOponente(usuario.fila, usuario, 'casual');

                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }

            console.log('USUÁRIO DISPONIVEL PARA FILA CASUAL');
        }

    });

    socket.on('entrarNaFila', async (data) => {

        const usuario = JSON.parse(data);
        usuario.socket = socket.id;
        socket.usuario = usuario;
        socket.idJogador = usuario.id;
        socket.entrouNaFila = true;


        console.log('Usuário solicitando para entrar na fila.. || id:' + usuario.id);
        if (usuario.id == null) {
            //VAI TOMAR DISCONECT DO socket.IO
            console.log('Solicitação para entrar na fila negada, motivo: sem id');
        } else {

            if (idJogadoresFila.some(elem => elem == usuario.id)) {
                console.log("Usuário ja na fila");
                return socket.emit('comunicacao', { 'msg': 'Usuario ja esta na fila' });
            } else {
                idJogadoresFila.push(usuario.id);
            }

            console.log('USUÁRIO DISPONIVEL PARA FILA');
            // Aqui vai colocar o usuário na fila
            //Fila Bronze
            if (usuario.pontuacao <= 499) {
                console.log("FILA DE BRONZE");
                usuario.fila = filaBronze;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
            //Fila Prata
            if (usuario.pontuacao >= 500 && usuario.pontuacao <= 699) {
                console.log("CAI NA FILA DE PRATA" + usuario.pontuacao);
                usuario.fila = filaPrata;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
            //Fila Ouro
            if (usuario.pontuacao >= 700 && usuario.pontuacao <= 899) {
                console.log("CAI NA FILA DE Ouro");
                usuario.fila = filaOuro;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
            //Fila Platina
            if (usuario.pontuacao >= 900 && usuario.pontuacao <= 1099) {
                console.log("CAI NA FILA DE Platina");
                usuario.fila = filaPlatina;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
            //Fila Diamante
            if (usuario.pontuacao >= 1100 && usuario.pontuacao <= 1399) {
                console.log("CAI NA FILA DE Diamante");
                usuario.fila = filaDiamante;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
            //Fila Mestre
            if (usuario.pontuacao >= 1400 && usuario.pontuacao <= 1799) {
                console.log("CAI NA FILA DE Mestre");
                usuario.fila = filaMestre;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
            //Fila Desafiante
            if (usuario.pontuacao >= 1800) {
                console.log("CAI NA FILA DE Desafiante");
                usuario.fila = filaDesafiante;
                const res = await buscarOponente(usuario.fila, usuario);
                if (res.usuarioNaFila) {
                    console.log("USUARIO TA NA FILA");
                } else {
                    console.log('Voltei Pro Main');
                    if (res[0].code === 200) {
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res[0].idPartida });
                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                    if (res[0].code === 400) {
                        console.log("AVISANDO USUARIOS");
                        io.to(res[1].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        io.to(res[2].socket).emit('comunicacao', { 'msg': 'Servidor de jogo indisponivel' });
                        console.log(usuario.fila);

                        var index = idJogadoresFila.indexOf(res[1].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                        var index = idJogadoresFila.indexOf(res[2].id);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }

                    }
                }
                return 0;
            }
        }

    });

    socket.on('sairDaFila', function () {
        if (socket.entrouNaFila) {
            console.log("Procurando usuário na fila");
            for (var i = 0; i < Object.keys(socket.usuario.fila.jogador).length; i++) {
                if (socket.usuario.fila.jogador[i].socket == socket.id) {
                    socket.usuario.fila.jogador.splice(i);
                    i = Object.keys(socket.usuario.fila.jogador.length);
                    console.log("Encontrei e removi");
                    var index = idJogadoresFila.indexOf(socket.idJogador);
                    if (index !== -1) {
                        idJogadoresFila.splice(index, 1);
                    }
                }
            }
        }
    });

    socket.on('enviarMsg', function (data) {
        socket.broadcast.emit("chat", data);
    });

    socket.on('desafiar', function (data) {
        io.to(data.socketId).emit('desafio', { 'socketId': socket.id, 'desafianteId': socket.idJogador, 'nome': data.nome });
    });

    socket.on('gerenciarDesafio', async function (data) {
        if (data.statusDesafio == "aceito") {
            console.log('desafio aceito');
            console.log(data);
            let obj = {
                'pecaBranca': {
                    'id': socket.idJogador
                },
                'pecaPreta': {
                    'id': data.info.desafianteId
                },
                'modo': 'casual'
            };
            console.log(obj);
            let res = await FilaController.usuarioDisponivelParaFila(obj);
            console.log(res);
            if (res.code == 200) {
                console.log("CAI AQUI");
                io.to(data.info.socketId).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res.idPartida });
                io.to(socket.id).emit('comunicacao', { 'msg': 'Partida Criada', 'idPartida': res.idPartida });
            }
        }

    });


    socket.on('estouDisponivel', function (data) {
        data.socket = socket.id;

        if (!socket.entrouNaFila) {
            for (var i = 0; i < Object.keys(jogadoresOnline.jogador).length; i++) {
                if (jogadoresOnline.jogador[i].id == socket.idJogador) {
                    return socket.broadcast.emit("gerenciarJogador", jogadoresOnline);
                }
            }
            jogadoresOnline.jogador.push(data);
            socket.broadcast.emit("gerenciarJogador", jogadoresOnline);
            io.to(socket.id).emit("gerenciarJogador", jogadoresOnline);
        }
    });

    socket.on('estouIndisponivel', function (data) {
        if (socket.entrouNaFila) {
            for (var i = 0; i < Object.keys(jogadoresOnline.jogador).length; i++) {
                if (jogadoresOnline.jogador[i].id == data.id) {
                    jogadoresOnline.jogador.splice(i);
                    break;
                }
            }
            socket.broadcast.emit("gerenciarJogador", jogadoresOnline);
            io.to(socket.id).emit("gerenciarJogador", jogadoresOnline);
        }
    });




    socket.on('disconnect', function () {

        if (socket.entrouNaFila) {
            console.log("USUARIO PICOU A MULA");
            console.log("Procurando usuário na fila");
            if (socket.usuario.fila) {
                for (var i = 0; i < Object.keys(socket.usuario.fila.jogador).length; i++) {
                    if (socket.usuario.fila.jogador[i].socket == socket.id) {
                        socket.usuario.fila.jogador.splice(i);
                        i = Object.keys(socket.usuario.fila.jogador.length);
                        console.log("Encontrei e removi");
                        var index = idJogadoresFila.indexOf(socket.idJogador);
                        if (index !== -1) {
                            idJogadoresFila.splice(index, 1);
                        }
                    }
                }
            }
        }

        if (socket.idJogador) {
            for (var i = 0; i < Object.keys(jogadoresOnline.jogador).length; i++) {
                if (jogadoresOnline.jogador[i].id == socket.idJogador) {
                    jogadoresOnline.jogador.splice(i);
                    break;
                }
            }
            socket.broadcast.emit("gerenciarJogador", jogadoresOnline.jogador);
        }


    });

});


server.listen(9090);