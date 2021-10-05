const express = require('express');
const app = express();
const server = require('http').createServer(app);
const io = require('socket.io')(server);
const { Chess } = require('chess.js');
const os = require("os");
var osu = require('node-os-utils')
var cpu = osu.cpu

const routes = require('./src/routes');
const ApiController = require('./src/controllers/ApiController');
const { Console } = require('console');

let partidas = { ativas: [] }; // Isso é usado para verificar se existe uma partida, assim o servidor não cria uma.
let partidasFinalizadas = { partida: [] };
let registroPartidasFinalizadas = [];

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


const countSeconds = (str) => {
    const [hh = '0', mm = '0', ss = '0'] = (str || '0:0:0').split(':');
    const hour = parseInt(hh, 10) || 0;
    const minute = parseInt(mm, 10) || 0;
    const second = parseInt(ss, 10) || 0;
    return (hour * 3600) + (minute * 60) + (second);
};

async function enviarPartidaBD() {
    if (Object.keys(partidasFinalizadas.partida).length > 0) {
        for (var i = 0; i < Object.keys(partidasFinalizadas.partida).length; i++) {
            let res = await ApiController.enviarPartidaBd(partidasFinalizadas.partida[i]);
            console.log("RESPOSTAAAAAAAAA");
            console.log(res);
            if (res.code == 200) {
                partidasFinalizadas.partida.splice(partidasFinalizadas.partida[i]);
            }
        }
    }
}

function finalizaPartida(partida, sala, ganhador = null, tipo) {
    console.log('FINALIZANDO PARTIDA');
    partida.fim = true;
    partida.status = tipo;
    partida.ganhador = ganhador;
    if (partida.threadTime) {
        clearInterval(partida.threadTime);
    }
    partidasFinalizadas.partida.push(partida);
    registroPartidasFinalizadas.push(sala);
    if (ganhador != null) {
        return io.sockets.in(sala).emit('comunicacao', { 'vencedor': toStringCorPeca(ganhador), 'acao': 'fimpartida' });
    }
    return io.sockets.in(sala).emit('comunicacao', { 'acao': 'fimpartida', 'empate': true });
}


function gerenciadorDeTempo(partida, sala) {
    if (partida.turno == 'w') {
        if (countSeconds(partida.tempoB) > 0) {
            partida.tempoB = new Date((countSeconds(partida.tempoB) - 1) * 1000).toISOString().substr(11, 8);
        } else {
            clearInterval(partida.threadTime);
            finalizaPartida(partida, sala, 'b', 'Tempo Excedido');
        }
    } else {
        if (countSeconds(partida.tempoP) > 0) {
            partida.tempoP = new Date((countSeconds(partida.tempoP) - 1) * 1000).toISOString().substr(11, 8);
        } else {
            clearInterval(partida.threadTime);
            finalizaPartida(partida, sala, 'w', 'Tempo Excedido');
        }
    }

    return io.sockets.in(sala).emit('comunicacao', { 'acao': 'tempo', 'tempoB': partida.tempoB, 'tempoP': partida.tempoP });


}

setInterval(enviarPartidaBD, 10000);


function mudarTurno(corAtual) {
    if (corAtual == 'w') {
        return 'b';
    }
    return 'w';
}

function toStringCorPeca(cor) {
    if (cor == 'w') {
        return 'BRANCA';
    }
    return 'PRETAS';
}

function cpuUsage() {

    // Take the first CPU, considering every CPUs have the same specs
    // and every NodeJS process only uses one at a time.
    const cpus = os.cpus();
    const cpu = cpus[0];

    // Accumulate every CPU times values
    const total = Object.values(cpu.times).reduce(
        (acc, tv) => acc + tv, 0
    );

    // Normalize the one returned by process.cpuUsage() 
    // (microseconds VS miliseconds)
    const usage = process.cpuUsage();
    const currentCPUUsage = (usage.user + usage.system) * 1000;

    // Find out the percentage used for this specific CPU
    const perc = currentCPUUsage / total * 100;

    return perc;
}


io.on('connection', socket => {
    var corPeca = null;
    console.log(`Socket conectado: ${socket.id}`);

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

    socket.on('entrarNaSala', async (data) => {
        const idUsuario = data.auth;
        const sala = data.idSala;
        //Se true peca branca se não peca preta
        const jogador1 = data.jogador1;


        if (registroPartidasFinalizadas.some(elem => elem == sala)) {
            socket.disconnect();
        }

        if (jogador1) {
            corPeca = 'w';
        } else {
            corPeca = 'b';
        }
        console.log('Usuário solicitando para entrar na sala.. || idUsuario:' + idUsuario + '|| Sala:' + sala + '|| PeçaBranca? ' + jogador1);
        if (idUsuario == null || sala == null || jogador1 == null) {
            //VAI TOMAR DISCONECT DO socket.IO
            console.log('SEM TOKEN OU SALA');
            return 0;
        } else {
            socket.join(sala);
            //começa aqui
            if (Object.keys(partidas.ativas).length > 0) {
                var encontrei = 0;
                console.log("PROCURANDO PARTIDA");

                for (var i = 0; i < Object.keys(partidas.ativas).length; i++) {
                    if (partidas.ativas[i].sala == sala) {
                        var SalaManager = partidas.ativas[i];
                        var encontrei = true;
                        i = Object.keys(partidas.ativas).length;
                        console.log("Encontrei");
                        partidas.ativas.splice(partidas.ativas[i]);
                    }
                } //FIM DO FOR

                if (!encontrei) {
                    console.log('Criando uma e adicionando...');
                    var partida = { 'sala': sala, 'fim': false, 'turno': 'w', 'tempoB': '00:05:00', 'tempoP': '00:05:00', 'status': 'aguardando', 'conectados': 1, 'chess': chess = new Chess(), 'respostaRendicaoW': 0, 'respostaRendicaoB': 0, 'qtdRendicaoW': 0, 'qtdRendicaoB': 0 }
                    partidas.ativas.push(partida);
                    console.log(partidas);
                } else {
                    console.log("ME CONECTANDO NA SALA");
                    if (SalaManager.conectados == 1) {
                        SalaManager.conectados = 2;
                        io.sockets.in(sala).emit('comunicacao', { 'partida': partida, 'acao': 'start' });
                        SalaManager.threadTime = setInterval(() => gerenciadorDeTempo(SalaManager, sala), 1000);
                    }
                }
            } else {
                console.log('Nenhuma partida no json, criando uma e adicionando...');
                var partida = { 'sala': sala, 'fim': false, 'turno': 'w', 'tempoB': '00:05:00', 'tempoP': '00:05:00', 'status': 'aguardando', 'conectados': 1, 'chess': chess = new Chess(), 'respostaRendicaoW': 0, 'respostaRendicaoB': 0, 'qtdRendicaoW': 0, 'qtdRendicaoB': 0 }
                partidas.ativas.push(partida);
                console.log(partidas);
                SalaManager = partida;
                SalaManager.n_turno = 0;
                SalaManager.partida = { movimento: [] };
                console.log("ME CONECTANDO NA SALA");
            }

            socket.on('movimentacao', async (data) => {

                if (corPeca == SalaManager.turno && SalaManager.chess.move({ from: data.source, to: data.target, promotion: data.promotion })) {
                    console.log("MOVIMENTO ACEITO");
                    socket.broadcast.to(sala).emit('comunicacao', { 'dados': data, 'acao': 'movimento' });
                    SalaManager.partida.movimento.push({ 'n_turno': SalaManager.n_turno, 'source': data.source, 'target': data.target, 'fen': data.fen });

                    if (SalaManager.chess.in_checkmate()) {
                        console.log("FIM DA PARTIDA, GANHADOR:" + SalaManager.turno);
                        finalizaPartida(SalaManager, sala, corPeca, 'Checkmate');
                        return io.sockets.in(sala).emit('comunicacao', { 'vencedor': toStringCorPeca(corPeca), 'acao': 'fimpartida' });

                    }

                    if (SalaManager.chess.in_draw()) {
                        console.log("FIM DA PARTIDA, EMPATE");
                        finalizaPartida(SalaManager, sala, null, 'Empate');
                        return io.sockets.in(sala).emit('comunicacao', { 'vencedor': null, 'acao': 'fimpartida' });
                    }
                    SalaManager.turno = mudarTurno(SalaManager.turno);
                    SalaManager.n_turno++;
                } else {
                    console.log("MOVIMENTO NÃO ACEITO ");
                }

            });
        }

        socket.on('solicitaEmpate', function (data) {
            if (SalaManager) {
                if (corPeca) {
                    if (SalaManager.conectados > 1) {
                        if (data.solicita) {
                            if (corPeca == 'w') {
                                if (SalaManager.turno == 'w') {
                                    if (!SalaManager.respostaRendicaoW) {
                                        if (SalaManager.qtdRendicaoW < 3) {
                                            SalaManager.respostaRendicaoW = 1;
                                            SalaManager.qtdRendicaoW = SalaManager.qtdRendicaoW + 1;
                                            return socket.broadcast.to(sala).emit('comunicacao', { 'acao': 'solicitaEmpate' });
                                        }
                                    }
                                }
                            } else {
                                if (SalaManager.turno == 'b') {
                                    if (!SalaManager.respostaRendicaoB) {
                                        if (SalaManager.qtdRendicaoB < 3) {
                                            SalaManager.respostaRendicaoB = 1;
                                            SalaManager.qtdRendicaoB = SalaManager.qtdRendicaoB + 1;
                                            return socket.broadcast.to(sala).emit('comunicacao', { 'acao': 'solicitaEmpate' });
                                        }
                                    }
                                }
                            }
                        } else {
                            if (data.aceitou) {
                                if (SalaManager.respostaRendicaoW || SalaManager.respostaRendicaoB) {
                                    if (corPeca == 'w') {
                                        SalaManager.respostaRendicaoW = 1;
                                        if (SalaManager.respostaRendicaoW && SalaManager.respostaRendicaoB) {
                                            finalizaPartida(SalaManager, sala, null, 'Empate');
                                        }
                                    } else {
                                        SalaManager.respostaRendicaoB = 1;
                                        if (SalaManager.respostaRendicaoW && SalaManager.respostaRendicaoB) {
                                            finalizaPartida(SalaManager, sala, null, 'Empate');
                                        }
                                    }
                                }

                            } else {
                                if (SalaManager.respostaRendicaoW || SalaManager.respostaRendicaoB) {
                                    SalaManager.respostaRendicaoW = 0;
                                    SalaManager.respostaRendicaoB = 0;
                                    return console.log('RECUSOU O EMPATE');
                                }
                            }
                        }
                    }
                }
            }
        });

        socket.on('informarendicao', function () {
            console.log(corPeca);
            if (SalaManager) {
                finalizaPartida(SalaManager, sala, corPeca === 'w' ? 'b' : 'w', 'Rendição');
            }

        });

        socket.on('disconnect', function () {
            console.log("TOMOU DISCONECT");
            console.log(corPeca);
            if (SalaManager) {

                if (registroPartidasFinalizadas.some(elem => elem == sala)) {
                    return 0
                } else {
                    finalizaPartida(SalaManager, sala, corPeca === 'w' ? 'b' : 'w', 'Desconexão');
                }
            }

        });

    });
});



//app.use(helmet());
app.use(express.json());
app.use(express.urlencoded({
    extended: false
}));


// Middleware de headers
app.use(function (req, res, next) {

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', '*');

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type,X-Access-Token');

    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);

    // Pass to next layer of middleware
    next();
});

app.use(routes);
server.listen(7000);
