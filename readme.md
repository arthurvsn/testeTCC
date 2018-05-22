# Teste TCC Arthur

# Laravel

# Solarium

<p>Consolidação de uma api que irá utilizar o Solarium Lucene para realização de buscas indexadas.</p>

# Como usar:

<p>PHP necessário: => 7.0 ou superior com Laravel instalado</p>
<ul>
    <li>
        Instale as dependencias
        <pre>composer install</pre>
    </li>
    <li>
        Inicie a base do Lucene, no arquivo config/solarium.php tem o nome da base da qual pode se instanciar
    </li>
    <li>
        Para iniciar o servidor da API crie o arquivo .env e adicione as informações conforme o .env.example está e rode o comando
        <pre>php artisa key:generate</pre>
        Após esse comando pode iniciar o servidor com o comando
        <pre>php artisan serve</pre>
        As informações de rotas utilizadas estão no diretorio routes/api.php
    </li>
    <li>
        # Altere o arquivo solarium.php na pasta de configurações, a posição core para a sua base instanciada no lucene.
    </li>
</ul>

### Desenvolvido por Arthur Vinícius
