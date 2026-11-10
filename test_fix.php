<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Compilers\BladeCompiler;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Get the Blade compiler
$compiler = $app->make(BladeCompiler::class);

// Test compiling a simple @forelse/@empty template
$template = '@forelse($items as $item)
{{ $item }}
@empty
No items
@endforelse';

$result = $compiler->compileString($template);
echo "=== Test 1: Simple @forelse/@empty ===\n";
echo $result."\n\n";

// Test with multiple @forelse/@empty pairs
$template2 = '@forelse($a as $x)
A
@empty
No A
@endforelse
@forelse($b as $y)
B
@empty
No B
@endforelse';

$result2 = $compiler->compileString($template2);
echo "=== Test 2: Multiple @forelse/@empty pairs ===\n";
echo $result2."\n\n";

// Check for invalid variable names
if (preg_match('/\$__empty_-\d+/', $result)) {
    echo "FAIL: Found invalid negative variable name in test 1\n";
} else {
    echo "PASS: No invalid negative variable names in test 1\n";
}

if (preg_match('/\$__empty_-\d+/', $result2)) {
    echo "FAIL: Found invalid negative variable name in test 2\n";
} else {
    echo "PASS: No invalid negative variable names in test 2\n";
}
