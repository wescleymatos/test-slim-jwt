<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/cliente', function ($request, $response, $args) {
    $data = $request->getServerParams();
    list($jwt) = sscanf($data['HTTP_AUTHORIZATION'], 'Bearer %s');

    if($jwt) {
        try {
            $dados = \JwtWrapper::decode($jwt);
            return $response->withJson($dados, 200);
        } catch(Exception $ex) {
            // nao foi possivel decodificar o token jwt
            return $response->withJson('Acesso nao autorizado', 400);
        }

    } else {
        // nao foi possivel extrair token do header Authorization
        return $response->withJson('Token nao informado', 400);
    }
});

$app->post('/auth', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();

    //$data = ['username' => $parsedBody['username'], 'pass' => $parsedBody['pass']];

    if($parsedBody['username'] == 'wescleymatos' && $parsedBody['pass'] == '123') {
        // autenticacao valida, gerar token
        $jwt = \JwtWrapper::encode([
            'expiration_sec' => 120,
            'iss' => 'localhost:8080',
            'userdata' => [
                'id' => 1,
                'name' => $parsedBody['username']
            ]
        ]);

        return $response->withJson([
            'login' => 'true',
            'access_token' => $jwt
        ], 200);
    }

    return $response->withJson([
        'login' => 'false',
        'message' => 'Login Inválido',
    ], 400);

    //return $response->withJson($data, 200);;
});
