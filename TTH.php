<?php

/**
 * Toy Tetragraph Hash
 *
 * Created by Rutger Broerze
 * March 2015
 */

class TTH {

    public function getHash($message)
    {
        $blocks = array();
        $alphabet = range('a', 'z') ;

        $running_total_1 = 0;
        $running_total_2 = 0;
        $running_total_3 = 0;
        $running_total_4 = 0;

        //ignore spaces, punctuation and capitalization
        $message_alpha = preg_replace("/[^a-zA-Z]+/", "", $message);
        $message_alpha_lowercase = strtolower($message_alpha);

        //if the message length is not divisible by 16 it's padded out with nulls
        $message_length = strlen($message_alpha_lowercase);
        if($message_length == 16)
        {
            $number_of_paddings = 0;
        } else {
            $number_of_paddings = 16 - ($message_length % 16);
        }

        //blocks of 16 letters, in 4 rows, converted to the corresponding numbers in the alphabet
        $block = 0;
        $row = 0;
        $block_character = 0;
        for($index = 0; $index < $message_length + $number_of_paddings; $index++)
        {
            //padded out with nulls
            if($index >= $message_length) {
                $number_for_block = 0;
            } else
            {
                $letter_from_message = substr($message_alpha_lowercase, $index, 1);
                $number_for_block = array_search($letter_from_message, $alphabet);
            }

            if($block_character % 4 == 0)
            {
                if(!$block_character == 0) {
                    $row++;
                }

                $blocks[$block][$row] = array();
            }

            array_push($blocks[$block][$row], $number_for_block);

            $block_character++;

            if($block_character == 16) {
                $block++;
                $row = 0;
                $block_character = 0;
            }
        }

        //two rounds of compression
        for($compess_index = 1; $compess_index < 3; $compess_index++)
        {
            foreach($blocks as $block)
            {
                //alter block for round two
                if($compess_index == 2)
                {
                    $block = $this->getBlockForRoundTwo($block);
                }

                $column_total_0 = 0;
                $column_total_1 = 0;
                $column_total_2 = 0;
                $column_total_3 = 0;

                for($index_row = 0; $index_row < 4; $index_row++)
                {
                    for($index_character = 0; $index_character < 4; $index_character++)
                    {
                        ${"column_total_$index_character"} += $block[$index_row][$index_character];
                    }
                }

                $running_total_1 = (($column_total_0 % 26) + $running_total_1) % 26;
                $running_total_2 = (($column_total_1 % 26) + $running_total_2) % 26;
                $running_total_3 = (($column_total_2 % 26) + $running_total_3) % 26;
                $running_total_4 = (($column_total_3 % 26) + $running_total_4) % 26;
            }
        }

        return $alphabet[$running_total_1] . $alphabet[$running_total_2] . $alphabet[$running_total_3] . $alphabet[$running_total_4];
    }

    private function getBlockForRoundTwo($block)
    {
        $block_for_round_2 = $block;

        $block_for_round_2[0] = $this->rotateArray($block[0]);
        $block_for_round_2[1] = $this->rotateArray($block[1], 2);
        $block_for_round_2[2] = $this->rotateArray($block[2], 3);
        $block_for_round_2[3] = array_reverse($block[3]);

        return $block_for_round_2;
    }

    private function rotateArray($array, $times = 1)
    {
        for($index = 0; $index < $times; $index++)
        {
            array_push($array, array_shift($array));
        }

        return $array;
    }
}