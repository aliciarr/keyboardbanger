<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Alias;
use DB;

class Tweet extends Model {
	
	
	protected $table = 'tweet';
	public $timestamps = false;
	
	public static function getTweetByTwitterId($id) {
		$tweet = Tweet::where('twitter_id', '=', $id)->get();
		if(count($tweet))
			return $tweet[0];
		return false;
	}
	
	public function buildFromTweet($tweet) {
		$this->twitter_id = $tweet->id;
		$this->text = $tweet->text;
		$this->timestamp = date(strtotime($tweet->created_at));
		$this->location = $tweet->coordinates;
	}
	
	public function setTarget($gang_id, $disposition = null) {
		$this->target = $gang_id;
		DB::table('tweet_target')->insert(
			array('tweet_id' => $this->id, 
			'member_id' => $this->member_id, 
			'gang_id' => $gang_id, 
			'disposition' => $disposition)
		);
	}


}

?>