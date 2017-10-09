param([bool]$fix = $false)
 
$lint = ".\php-cs-fixer-v2.phar"
if (-not (Test-Path $lint)) { 
	wget http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -OutFile php-cs-fixer-v2.phar
}

if ($fix -eq $false) 
{
	Write-Host "Doing Dry Run";
	& php php-cs-fixer-v2.phar fix  -v --dry-run --stop-on-violation --using-cache=no --rules=-line_ending .
}
else
{
	Write-Host "Fixing code";
	& php php-cs-fixer-v2.phar fix -v --using-cache=no --rules=-line_ending .
}