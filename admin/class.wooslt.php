<?php
    
    if ( ! defined( 'ABSPATH' ) ) { exit;}
    
    class MAE
        {
            var $licence;
            
            var $interface;
            
            /**
            * 
            * Run on class construct
            * 
            */
            function __construct( ) 
                {
                    // $this->licence              =   new MAE_licence(); 

                    $this->interface            =   new MAE_options_interface();
                     
                }
                
              
        } 
    
    
    
?>