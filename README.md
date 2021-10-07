<H1>Teste de conclusão do curso de análise e desenvolvimento de sistemas | Nota: 8.5 </H1>
<H2>ChessIF: Sistema de Gerenciamento de jogos de xadrez utilizando arquitetura de microsserviços </H2>
<H3>Resumo</H2>
Esse trabalho tem como objetivo planejar e desenvolver um sistema de gerenciamento de partidas de xadrez, utilizando a arquitetura de software: micro serviços; o sistema foi desenvolvido e distribuído três serviços independentes: web, fila e jogo. Cada serviço é responsável por uma parte de aplicação e sendo assim caso algum serviço caia o sistema inteiro não estará comprometido. O sistema tem como objetivo gerenciar partidas de xadrez e prover conquistas adquiridas dentro do sistema, essas conquistas se denominam como títulos, são alcançadas jogando partidas de xadrez e exibidas no mural de pontuação do sistema chamado de ranking. Os usuários do sistema podem acessar a partir de qualquer dispositivo que contenha um navegador atualizado e internet. Alunos de todos os campus podem se cadastrar e jogar com adversários do mesmo nível, caso o modo de jogo escolhido seja o ranqueado, os administradores tem a tarefa de cadastrar os campus e gerenciar os usuários e títulos cadastrados.

<H4>Possiveis Melhorias</H4>
<li>Aplicar uma criptografia de chave simétrica nos dados do usuário enviados para o front-end</li>

<h3> Como rodar? </h3>
Dentro de cada pasta, contém instruções de como instalar cada servidor, ligue todos os servidores para o funcionamento correto.
Portas usadas:
<li>Servidor de fila/chat -> HTTP:9090 </li>
<li>Servidor de jogos -> HTTP:7000 </li>
<li>Servidor principal -> HTTP:8000 (Porta padrão pelo "artisan serve")</li>


<h3>Diagrama de Implantação</h3>
<img src="https://github.com/Golden-Sheep/chess_if/blob/main/Diagrama%20de%20implanta%C3%A7%C3%A3o.png">
