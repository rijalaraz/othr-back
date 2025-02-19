<?php

namespace App\DataFixtures;

use Faker\Provider\Youtube;

class YoutubeGenerator extends Youtube 
{
    public function youtubeId()
    {
        $aYoutubeId = [
            'zvMKCTxDOx8',
            'SuuuygYuvCQ',
            'Pf02qq-X3o4',
            'z4cEWZw4iJU',
            'kQC60ihFN8A',
            'kkAZRTm-nsY',
            'nO-DYiIst2g',
            'opKCl0yl1nM',
            'MCpxUjx-33E',
            'ToMrI2tIbLY',
            'IlKGPr3jTqg',
            '8q-sxQ2EobM',
            'bhj0qoKv4ew',
            'o9zsfZ3q5Cw',
            'YZ-LagCs6GA',
            'deVrs2JTZsM',
            'BWreJBcjE1Q',
            'IKCyOaK_ML4',
            'Ez22sVqIvog',
            'U317c_mNYrk',
            'ZaTJ4qAC1tU',
            'N5Q6scicBxo',
            'iM3HvMW2stk',
            '5NsFZYMb4yU',
            'S6gO91DQMhE',
            'ENSstnwzKEc',
            'xwdjc_w08rM',
            'E8O-a-ripkw',
            'jcjkYRWFQpg',
            'cN4cDyKlRCE'
        ];

        $indexYoutube = array_rand($aYoutubeId);
        $id = $aYoutubeId[$indexYoutube];

        return $id;
    }
}