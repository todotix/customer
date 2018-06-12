<?php

namespace Todotix\Customer\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Validator;
use Asset;
use AdminList;
use AdminItem;
use PDF;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TestController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->prev = $url->previous();
	  $this->module = 'test';
	}

    public function getEncryptionTest($texto = 'Texto de muestra') {
        $response = '<strong>Comenzando la prueba...</strong>';
        $encrypted = \Pagostt::encrypt($texto);
        $response .= '<br><br><strong>Texto Encriptado:</strong> '.$encrypted;
        $decrypted = \Pagostt::decrypt($encrypted);
        $response .= '<br><br><strong>Texto Decifrado:</strong> '.$decrypted;
        return $response;
    }

    public function getDecryptionTest($textoEncriptado) {
        $response = '<strong>Comenzando la prueba...</strong>';
        $decrypted = urldecode($textoEncriptado);
        $response .= '<br><br><strong>Texto Decodeado:</strong> '.$decrypted;
        $decrypted = \Pagostt::decrypt($decrypted);
        $response .= '<br><br><strong>Texto Decifrado:</strong> '.$decrypted;
        return $response;
    }

	public function getGeneralTest() {
        if(\App::environment('local')){
            $response = '<strong>Comenzando la prueba...</strong>';
            
            $items = \Todotix\Master\App\NodeTranslation::where('singular', 'like', '%model.%')->groupBy('singular')->orderBy('singular')->get();
            if(count($items)>0){
            	$response .= '<br><br><strong>Node Translation.</strong> Model:';
                foreach($items as $item){
                    $response .= $this->getTransResponse($item->singular, ":model.");
                }
            }
            
            $items = \Todotix\Pagostt\App\FieldTranslation::where('label', 'like', '%fields.%')->groupBy('label')->orderBy('label')->get();
            if(count($items)>0){
                $response .= '<br><br><strong>Field Translation.</strong> Agregar fields.php:';
                foreach($items as $item){
                	$response .= $this->getTransResponse($item->label, "master::fields.");
                }
            }
            
            $items = \Todotix\Pagostt\App\FieldOptionTranslation::where('label', 'like', '%admin.%')->groupBy('label')->orderBy('label')->get();
            if(count($items)>0){
                $response .= '<br><br><strong>Field Option Translation.</strong> Agregar a admin.php:';
                foreach($items as $item){
                    $response .= $this->getTransResponse($item->label, "master::admin.");
                }
            }

            $items = \Todotix\Pagostt\App\MenuTranslation::where('name', 'like', '%admin.%')->groupBy('name')->orderBy('name')->get();
            if(count($items)>0){
                $response .= '<br><br><strong>Menu Translation.</strong> Agregar a admin.php:';
                foreach($items as $item){
                	$response .= $this->getTransResponse($item->name, "master::admin.");
                }
            }

            $nodes_array = \Todotix\Master\App\Node::where('location', 'app')->lists('id');
            $items = \Todotix\Pagostt\App\Field::whereIn('parent_id', $nodes_array)->where('type', 'text')->get();
            if(count($items)>0){
                $response .= '<br><br><strong>Textos largos.</strong> Revisar si es necesario editar nodes.xls:';
                foreach($items as $item){
                    if(!$item->field_extras()->where('type', 'class')->where('value', 'textarea')->first()){
                		$strong = $this->checkIfStrong($item->name, ['content','description']);
                		$response .= $strong['begin'];
                		$response .= "<br>- ".$this->generateLink(url('admin/model/'.$item->parent->name.'/create'), $item->parent->singular);
                        $response .= ' ('.$item->parent->name.') - '.$item->label.' ('.$item->name.')';
                		$response .= $strong['end'];
                    }
                }
            }

            $nodes = \Todotix\Master\App\Node::where('location', 'app')->get();
            if(count($nodes)>0){
                $response .= '<br><br><strong>Listado de Nodos.</strong> Revisar si el listado de nodos es correcto:';
                foreach($nodes as $node){
                	$response .= "<br>- ".$this->generateLink(url('admin/model-list/'.$node->name), $node->name)." -> Nº (count)";
                	foreach($node->fields()->displayList('show')->where('type', '!=', 'field')->get() as $field){
                		$strong = $this->checkIfStrong($field->type, ['text','subchild']);
                		$response .= $strong['begin'];
                		$response .= ' - '.$field->label.' ('.$field->type.')';
                		$response .= $strong['end'];
                	}
                }
            }

            $nodes = \Todotix\Master\App\Node::where('location', 'app')->where('dynamic', 0)->get();
            if(count($nodes)>0){
                $response .= '<br><br><strong>Revisión general de permisos.</strong> Revisar si los permisos están bien:';
                foreach($nodes as $node){
                    $model = \FuncNode::node_check_model($node);
                    $rules_edit = $model::$rules_edit;
                    $rules_create = $model::$rules_create;
                    $fields_array = $node->fields()->where('type', '!=', 'child')->displayItem(['admin', 'show'])->whereNull('child_table')->lists('name')->toArray();
                    // REVISAR SI HAY REGLAS QUE SOBRAN
                    $required_fields = $this->checkRules($rules_create, $rules_edit, $fields_array);
                    if(count($required_fields)>0){
                        $response .= "<br>- Quitar de modelo (rules): ".$this->generateLink(url('admin/model/'.$node->name.'/create'), $node->name);
                        foreach($required_fields as $field_name => $field_rule){
                            $response .= ' - '.$field_name.' ('.$field_rule.')';
                        }
                    }
                    // REVISAR SI HAY REGLAS QUE FALTAN
                    $fields = $node->fields()->where('type', '!=', 'child')->displayItem(['admin', 'show'])->whereNull('child_table')->get();
                    $pending_fields = $this->checkPendingRules($fields, ($rules_edit + $rules_create));
                    if(count($pending_fields)>0){
                        $response .= "<br>- Agregar regla a (rules): ".$this->generateLink(url('admin/model/'.$node->name.'/create'), $node->name);
                        foreach($pending_fields as $field){
                            $response .= ' - '.$field;
                        }
                    }
                }
            }

            $response .= '<br><br><strong>Finalizaron las pruebas.</strong>';
        } else {
            $response = 'No autorizado.';
        }
        print_r($response);
	}

}