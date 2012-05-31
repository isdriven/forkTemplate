<?php
/**
 * Fork Template
 * Simple And Powerful Template Engine For PHP
 *
 * @Author : Ippei Sato
 * @License : MIT License
 * @Version : 0.5
 */

class forkTemplate
{
    private $target;
    private $tag;
    private $dir;
    private $e;
    private $data = array();
    private $start_tag = "{@";
    private $end_tag = "}";
    private $constants;
    private $tmp = null;
    private $session;
    public function __construct( $dir , $e = "php" )
    {
        $this->dir = $dir;
        if( $this->dir[ strlen( $this->dir ) - 1 ] !== '/' ){
            $this->dir = $this->dir.'/';
        }
        $this->e = "." . $e;
        $this->constant = new stdClass;
        $this->session = new stdClass;
    }
    public function setConstant( $name , $value ){
        $this->constants->{$name} = $value;
    }
    public function setSessionValue( $name , $value ){
        $this->session->{$name} = $value;
    }
    public function target( $name )
    {
        if( isset( $this->target ) ){
            $this->val("");
        }
        $this->target = $name;
        return $this;
    }
    public function snippet( $name )
    {
        $this->snippet =  $name;
        return $this;
    }
    public function tag( $name , $styles = array(), $attributes = array()){
        $this->tag = array( $name , $styles , $attributes );
        return $this;
    }
    public function val()
    {
        $val = func_get_args();
        if( !isset( $this->target ) ){
            return false;
        }
        if( isset( $this->tmp ) ){
            $this->tmp = $this->replaceTarget( $this->tmp , $this->target , $this->exec( $val ));
        }else{
            $this->tmp = $this->exec( $val );            
        }

        if( !isset( $this->init_target ) ){
            $this->init_target = $this->target;
        }
        $this->tag = null;
        $this->snippet = null;
        $this->val = null;

        return $this;
    }
    public function set()
    {
        if( isset( $this->snippet ) ){
            $this->val("");
        }
        if( isset( $this->data[$this->init_target] ) ){
            $this->data[$this->init_target] .= $this->tmp;
        }else{
            $this->data[$this->init_target] = $this->tmp;
        }
        $this->tmp = null;
        $this->target = null;
        $this->init_target = null;
        return $this;
    }

    public function render( $contents )
    {
        if( !empty( $this->data ) ){
            foreach( $this->data as $k => $v ){
                $contents = $this->replaceTarget($contents , $k , $v);
            }
        }
        // constants
        if( !empty( $this->constants ) ){
            foreach( $this->constants as $k => $v ){
                $contents = $this->replaceConstants($contents , $k , $v);
            }
        }
        
        $contents = preg_replace( "/\{@[a-zA-Z0-9\-_]+\}/" , "" , $contents );
        $contents = preg_replace( "/\{@![a-zA-Z0-9\-_]+\}/" , "" , $contents );

        $this->clean();
        $this->contents = $contents;
        return $this->contents;
    }
    public function replaceTarget( $contents , $target , $replace ){
        return str_replace( $this->start_tag . $target  . $this->end_tag , $replace  , $contents );
    }

    public function replaceConstants( $contents , $target , $replace ){
        return str_replace( '{@!' . $target  . '}' , $replace  , $contents );
    }

    public function clean()
    {
        $this->data = array();
        $this->snippet = null;
        $this->tag = null;
        $this->val = null;
        $this->target = null;
    }
    private function exec( $val )
    {
        if( isset( $this->snippet ) ){
            $snippet = $this->snippet;
            $session = $this->session;
            if( ( $file_name = $this->existSnippetFile( $snippet ) ) !== false ){ 
                ob_start();
                include( $file_name );
                $buffer = ob_get_clean();
                return $buffer;
            }else{
                return $val[0];
            }
        }else if( isset( $this->tag ) ){
            return $this->constructTag( $val );
        }else{
            return $val[0];
        }
    }
    private function existSnippetFile( $name )
    {
        $file =  $this->dir . $name . $this->e ;
        if( file_exists( $file ) ){
            return $file;
        }else{
            return false;
        }
    }
    private function constructTag( $val ){
        if( empty( $val ) ){
            $val[] = "";
        }

        $val = $val[0];

        $styles = $this->tag[1];
        $style = array();
        foreach( $styles as $k=>$v ){
            $style[] = sprintf( "%s:%s" , $k , $v );
        }
        $style = sprintf( 'style="%s"' , implode( ";" , $style ));

        $attributes = $this->tag[2];
        $attribute = array();
        foreach( $attributes as $k=>$v ){
            $attribute[] = sprintf( '%s="%s"' , $k , $v );
        }
        $attribute = implode( " " , $attribute );

        $single_tag = false ;
        if( $this->tag[0][ strlen( $this->tag[0] ) - 1 ] == '/' ){
            $single_tag = true;
            $this->tag[0] = str_replace( '/' , '' , $this->tag[0] );
        }

        if( $single_tag ){
            $tag = sprintf( "<%s %s %s />" , $this->tag[0] , $style , $attribute );
        }else{
            $tag = sprintf( "<%s %s %s>%s</%s>" , $this->tag[0] , $style , $attribute , $val , $this->tag[0]);
        }
        return $tag;
    }
}
