<?php

$manager = $this->getContainer()->get('h4cc_alice_fixtures.manager');

// Get a FixtureSet with __default__ options.
$set = $manager->createFixtureSet();
$set->addFile(__DIR__.'/Users.yml', 'yaml');
$set->addFile(__DIR__.'/Categories.yml', 'yaml');
$set->addFile(__DIR__.'/Products.yml', 'yaml');

return $set;