<?php
/**
 * Fork Template
 * Simple And Powerful Template Engine For PHP
 *
 * @Author : Ippei Sato
 * @Lisence : MIT
 */

include( 'forkTemplate.php' );

$f = new forkTemplate( dirname( __FILE__ ) . "/snippet" , "php" );

$f->setSessionValue( 'get' , array('name'=>'john') );

$f->target( "body" )->snippet( 'sample' )->val("--> inner snippet(sample.php). {@me} <br />this is output from sample, session value--> ")
->target( "me" )->val( "{{nest}}" )->set();

$f->target("another")
->tag('div' , array('color'=>'red', 'font-size'=>20) , array('name'=>'ippei'))->val( "another one by tag method")->set();

$f->target("image")->tag('img/' , array() , array('src'=>'http://www.croisforce.com/ips-wp/wp-content/themes/first-theme/logo/logo.gif'))->val()->set();

$f->target("body")->val( "if point same target , it will be added." )->set();

$f->setConstant( "con" , "{{can use constant value}}" );

echo "<span>hi there. this is forktemplate demo</span>";
echo $f->render( "{@body} <br />{@!con} <br />{@another}<br />{@image}");
