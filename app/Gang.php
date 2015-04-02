<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Alias;

class Gang extends Model {
	
	
	protected $table = 'gang';
	
	public function getAliases() {
		
		$aliases = Alias::where('gang_id','=',$this->id)->get();
		return $aliases;
		
	}


}

?>