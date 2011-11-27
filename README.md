# Simple PHP Router class

A simple Router class that supports REST routing, named routes and reverse routing. 

## Usage

    <?php
    require 'Router.php';

    $r = new Router();

    // maps / to controller 'users' and method 'index'.
    $r->match('/','users#index');

    // maps /user/5 to controller 'users', method 'show' with parameter 'id' => 5
    $r->match('/user/:id','users#show');

    // maps POST request to /users/ to controller 'users' and method 'create'
    $r->match('/users','users#create',array('via' => 'post'));


    // maps /users/5/edit to controller 'users', method 'edit' with parameters 'id' => 5.
    $r->match('/user/:id/edit','users#edit',array('via' => 'get', 'as' => 'user_edit_page'));

    // echoes /users/5/edit
    echo $r->url_for_route('user_edit_page',array('id' => '5'));


    if($r->hasRoute()) {
        extract($r->getRoute());
        ?>
        <h1>Route found!</h1>
        <p><b>Controller: </b><?php echo $controller; ?></p>
        <p><b>Action: </b><?php echo $action; ?></p>
        <p><b>Params: </b><?php var_dump($params); ?></p>
        <?php
    } else {
        ?><h1>No route found.</h1><?php
    }

Have a look at example.php or read trough the class' documentation for a better understanding on how to use this class.