#!/usr/bin/env perl

# [SmartFramework / Task Engine / TEST-Sample Task]
# (c) 2006-2016 unix-world.org - all rights reserved
# r.160627


###
### Test Sample Task: Hit One URL (using CURL) #####
###

my $task_name = "TEST.Task";
my $task_ini = "task-template.ini";

###
### INFO: This Script can be customized as you need, this is just a sample of a TASK Script (Sample) ###
###


######################################## PERL MODULES

use strict;
use warnings;
use Cwd;
use Time::HiRes;
use Term::ANSIColor;

######################################## TERM COLORS

my $clr_error = ['bold bright_white on_red'];
my $clr_warn = ['bold bright_white on_bright_red'];
my $clr_notice = ['bold black on_bright_cyan'];
my $clr_ok = ['bold bright_white on_green'];

######################################## CHECK ARGUMENTS

my $num_args = $#ARGV + 1;
if($num_args != 1) {
	print colored($clr_error, "TASK.ERR : ".$task_name." STOP: The ID parameter is missing (script must have 1 parameter) ...");
	print "\n";
	exit;
}
my $id = $ARGV[0];

######################################## PARSE INI SETTINGS {{{SYNC-PERL-INIPARSE}}} ### DO NOT EDIT THIS SCRIPT !!! ### USE task-template.ini to store all settings ###

my %inisett = ();
my $cfname;
my $cfval;
open(CONF, $task_ini) || die "Failed to open config file ".$task_ini;
while(<CONF>) {
	s/\r|\n//g;
	if (/^#/ || !/\S/) {
		next;
	}
	/^([^=]+)=(.*)$/;
	$cfname = $1;
	$cfval = $2;
	$cfname =~ s/^\s+//g;
	$cfname =~ s/\s+$//g;
	$cfval =~ s/^\s+//g;
	$cfval =~ s/\s+$//g;
	$inisett{$cfname} = $cfval;
}
close(CONF);

my $user = str_single_quotes_escapeshellarg($inisett{'TaskURLAuthUser'});
my $pass = str_single_quotes_escapeshellarg($inisett{'TaskURLAuthPassword'});
my $url = str_single_quotes_escapeshellarg($inisett{'TaskURLAddr'});

######################################## RUNTIME

sleep 5; # this sample task execution is to wait 5 seconds (it can be changed as below ... see commented code ...)

my $task_output = 777; # task result

if($url ne "") {
	my $auth = "";
	if($user ne "") {
		if($pass ne "") { # use auth: user and pass
			$auth = " --basic -u '".str_single_quotes_escapeshellarg($user).":".str_single_quotes_escapeshellarg($pass)."'";
		} else { # use auth: only user
			$auth = " --basic -u '".str_single_quotes_escapeshellarg($user)."'";
		}
	}
	$task_output = `curl -s -o /dev/null -w '%{http_code}' --get --connect-timeout 30 --max-time 600${auth} --url '${url}'`; # use auth with user and pass
}

######################################## EVAL RESULT

if($task_output != 200) {
	if($task_output != 202) {
		print colored($clr_warn, "TASK.ERR # ${id} : ".$task_name." COMPLETED: ${task_output}");
		print "\n";
	} else {
		print colored($clr_notice, "TASK.WARN # ${id} : ".$task_name." COMPLETED: ${task_output}");
		print "\n";
	}
} else {
	print colored($clr_ok, "TASK.INF # ${id} : ".$task_name." COMPLETED SUCCESSFUL: ${task_output}");
	print "\n";
}

######################################## INTERNAL FUNCTIONS {{{SYNC-PERL-FXS}}}

sub str_single_quotes_escapeshellarg {
	my $arg = shift;
	$arg =~ s/'/'\\''/g; # escape single quotes
	return "".$arg;
}

sub str_begins_with {
	my $ok = "";
	if(substr($_[0], 0, length($_[1])) eq $_[1]) {
		$ok = "OK";
	}
	return "".$ok;
}

######################################## EXIT

exit 0;

#END
