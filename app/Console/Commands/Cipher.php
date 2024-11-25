<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Cipher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cipher {text : The text to encrypt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shifts alphabetic characters in the text by 5 positions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->argument('text');
        $result = $this->shiftText($input);
        $this->info("Original: $input");
        $this->info("Encrypted: $result");
    }

    /**
     * Shifts alphabetic characters in the text by 5 positions
     */
    private function shiftText(string $text): string
    {
        return preg_replace_callback('/[a-zA-Z]/', function($match) {
            $char = $match[0];
            $isUpper = ctype_upper($char);
            $base = $isUpper ? ord('A') : ord('a');
            
            // Convert to 0-25 range, add 5, wrap around with modulo, then convert back
            $shifted = (ord($char) - $base + 5) % 26 + $base;
            
            return chr($shifted);
        }, $text);
    }
}
