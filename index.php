<?php
/**
 * Fork Template
 * Simple And Powerful Template Engine For PHP
 *
 * @Author : Ippei Sato
 * @Lisence : MIT
 */

include( 'forkTemplate.php' );

$f = new forkTemplate( dirname( __FILE__ ) . "/tags" , "php" );
$f->target( "body" )->snippet( 'exsample' )->val("hello template! this is in [exsample] tag. {@me}")
->target( "me" )->val( "[this is chain target]" )->set();

$f->target("another")->tag('div' , array('color'=>'red', 'font-size'=>20) , array('name'=>'ippei'))->val( "[this is another target]")->set();
$f->target("anotherimage")->tag('img/' , array() , array('src'=>'http://www.croisforce.com/ips-wp/wp-content/themes/first-theme/logo/logo.gif'))->val()->set();

$f->target("body")->val( "[additional to body]" )->set();

$f->setConstant( "con" , "[this is constant value]" );

echo $f->render( "{@body} <br />{@!con} <br />{@another}<br />{@anotherimage}");


