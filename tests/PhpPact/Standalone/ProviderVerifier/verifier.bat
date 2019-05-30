@ECHO OFF

REM this script simulates a command (like pact-verifier) which prints several lines to stdout and stderr

ECHO "first line"
ECHO "second line" 1>&2

exit 42