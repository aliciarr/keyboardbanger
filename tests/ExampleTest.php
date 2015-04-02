<?php

use App\Gang as Gang;
use App\GangMember as GangMember;
use App\Alias as Alias;
use App\Tweet as Tweet;

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$response = $this->call('GET', '/');

		$this->assertEquals(200, $response->getStatusCode());
	}
	
	public function testTwitter()
	{
		$response = Twitter::getSearch(['q' => 'roeblock', 'count' => 3]);
		//print_r($response->statuses);
	}
	
	public function testDB()
	{
		$gangs = Gang::get();
		//print_r($gang[1]->name);
		$gang = $gangs[0];
		$aliases = $gang->getAliases();
		//print_r($aliases);
		//$gang_member = GangMember::getMemberByTwitterId(2163923347);
		//if(count($gang_member))
		//	print_r($gang_member);
		
	}
	
	public function testSearchTwitterByAlias()
	{
		//get all aliases

		$all_aliases = Alias::all();	
		
		$gang_tweet_array = array();
		$gang_beef_array = array();
		
		$gangs = Gang::all();
		foreach($gangs as $gang)
		{
			$aliases = $gang->getAliases();
			foreach($aliases as $alias) 
			{
				// Do a Twitter Search for each alias in the DB
				$response = Twitter::getSearch(['q' => $alias->alias, 'count' => 100, 'result_type' => 'recent']);
				foreach($response->statuses as $status)
				{
					// Look at each retreived tweet and check user name for a gang alias (regular expression)
					if (preg_match("/$alias->alias/i", $status->text)) {
						$added_to_gang_tweet_array = false;
						$target = intval($gang->id);
						foreach($all_aliases as $alias_match)
						{
							// Skip if alias is "opps"
							if($alias_match->gang_id == 7)
								continue;
								
							$alias_to_use = $alias_match->alias;
							
							if (preg_match("/$alias_to_use/i", $status->user->name) || preg_match("/$alias_to_use/i", $status->user->description)) {
								//dd($status->user);
								$source = intval($alias->gang_id);
								
								// Only add tweets where the source gang is not equal to the target gang
								if($source != $target && $status->user->lang == 'en')
								{
									// If the gang member isn't in our database, add him
									$member = GangMember::getMemberByTwitterId($status->user->id);
									if(!$member) {
										$member = new GangMember();
										$member->gang_id = $source;
										$member->buildFromTwitterUser($status->user);
										//dd($member);
										$member->save();
									}
									
									//Add the tweet
									$tweet = Tweet::getTweetByTwitterId($status->id);
									if(!$tweet) {
										$tweet = new Tweet();
										$tweet->member_id = $member->id;
										$tweet->buildFromTweet($status);
										$tweet->save();
										// Save First, then set target
										$tweet->setTarget($target);
									}
									
									
								}
								
							}
						}
					}
					
				}
			}
		}
		
	}

}
