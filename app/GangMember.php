<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Alias;

class GangMember extends Model {
	
	
	protected $table = 'member';
	public $timestamps = false;
	
	public static function getMemberByTwitterId($id) {
		$member = GangMember::where('twitter_id', '=', $id)->get();
		if(count($member))
			return $member[0];
		return false;
	}
	
	// Fill in instantiated GangMember using user object from Twitter API
	public function buildFromTwitterUser($user) {
		$this->twitter_id = $user->id;
		$this->name = $user->name;
		$this->twitter_handle = $user->screen_name;
	}


}

?>