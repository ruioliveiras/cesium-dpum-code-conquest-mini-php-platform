# Uso: invocar a script passando-lhe o ficheiro que contém o grafo e o ficheiro que contém a solução
# Esta script imprime a pontuação ou 0 caso algo esteja errado

use warnings;
use strict;

my $graph;

my $cliques;

sub is_clique {
	my @clq;

	for my $u (@_) {
		for my $v (@clq) {
			return 0 unless $graph->{$u}{$v};
		}
		push @clq, $u;
	}
	return 1;
}

use Data::Dumper;
$Data::Dumper::Indent = 0;

my $graph_file = shift;
my $sol_file = shift;

my $fh;
open $fh, $graph_file or die "Couldn't open graph $graph_file: $!";

while(<$fh>) {
	chomp;
	my @l = split;
	my $u = shift @l;
	for my $v (@l) {
		$graph->{$u}{$v} = 1;
		$graph->{$v}{$u} = 1;
	}
}
close $fh;

open $fh, $sol_file or die "Couldn't open solution $sol_file: $!";

my $sol = <$fh>;
close $fh;

my ($n, @rest) = split /\s+/, $sol;

my $res = 0;

$res = $n if $n == scalar @rest &&  is_clique(@rest);
printf "10%d\n", $res;
