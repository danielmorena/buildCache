<?php

kirby()->routes(array(
	array(
		"pattern" => "purgeCache",
		"action"  => function() {
			kirby()->cache()->flush();
			return true;
		}
	)
));