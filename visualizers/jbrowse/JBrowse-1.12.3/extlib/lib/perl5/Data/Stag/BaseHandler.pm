# $Id: BaseHandler.pm,v 1.34 2008/06/03 17:31:15 cmungall Exp $
#
# This  module is maintained by Chris Mungall <cjm@fruitfly.org>

=head1 NAME

  Data::Stag::BaseHandler     - Base class for writing tag stream handlers

=head1 SYNOPSIS

  # EXAMPLE 1
  package MyPersonHandler;
  use base qw(Data::Stag::BaseHandler);

  # handler that prints <person> nodes as they are parsed;
  # after each <person> node is intercepted, it is discarded
  # (it does not go to form the final tree)
  sub e_person {
      my $self = shift;
      my $node = shift;
      printf "Person name:%s address:%s\n",
        $node->sget('name'), $node->sget('address');
      return;               # prune this from tree
  }
  1;
  
  # EXAMPLE 2
  package MyStatsHandler;
  use base qw(Data::Stag::BaseHandler);

  # handler that modifies tree as it goes
  # changes <measurement><unit>inch</unit><quantity>10</quantity></measurement>
  # to      <measurement><unit>cm</unit><quantity>25</quantity></measurement>
  sub e_measurement {
      my $self = shift;
      my $node = shift;
      if ($node->sget('unit') eq 'inch') {
          $node->set('unit', 'cm');
          $node->set('quantity', $node->get('quantity') * 2.5);
      }
      return $node;     # replace <measurement> with new data in result tree
  }
  1;
  
  # Using the handlers
  my $handler = MyHandler->new;
  my $stag = Data::Stag->parse(-fh=>$fh, -handler=>$handler);

  # Using a handler from the command line:
  unix> stag-handle.pl -m MyHandler input.xml > post-processed.xml

=cut

=head1 DESCRIPTION

Default Simple Event Handler, other handlers inherit from this class

See also L<Data::Stag> and L<Data::Stag::BaseGenerator>

Stag has an event-handling architecture; parsers or generators
B<generate> or B<fire> events. Events can be hierarchical/nested, just
like stag nodes. These events are caught by handlers. By default,
uncaught events stack to form stag trees.

Stag has built in parsers for parsing xml, sxpr and itext data. You
can construct your own parsers for dealing with your own formats
specific to your own data; these should inherit from
L<Data::Stag::BaseGenerator>

Stag also has built in handlers for these formats. You can construct
your own - either as modules that inherit from this one, or as hashes
of anonymous subroutines.

If you wish to write your own handler that writes out to another
format, you may wish to inherit from L<Data::Stag::Writer>

=head2 CATCHING EVENTS

This class catches Data::Stag node events  (start, end and body) and allows the
subclassing module to intercept these. Unintercepted events get pushed
into a tree. The final tree is returned at the end of a parse() call

This class can take SAX events and turn them into simple
Data::Stag events

the events recognised are

  start_event(node-name)
  evbody(node-data)
  end_event(node-name)

and also

  event(node-name, node-data|[nodes])

which is just a wrapper for the other events

you can either intercept these methods; or you can define methods


  s_<element_name>
  e_<element_name>

that get called on the start/end of an event; you can dynamically
change the structure of the tree by returning nodes from these methods.

  # the follow handler prunes <foo> nodes from the tree, and writes
  # out data from the <person> node
  # when parsing large datasets, it can be a good idea to prune nodes
  # from the tree, so the result tree of the parse is not too big
  my $h = Data::Stag->makehandler( foo => 0,
				   person => sub {
				       my $self = shift;
				       my $node = shift;
				       printf "Person name:%s address:%s\n",
					 $node->sget('name'), $node->sget('address');
				       return;
				   });
  my $parser = MyParser->new;
  $parser->handler($h);
  $parser->parse(-fh=>$fh);
  my $result_tree = $h->stag;


=head1 PUBLIC METHODS - 

=head3 new

       Title: new

        Args: 
      Return: L<Data::Stag::BaseHandler>
     Example: 

returns the tree that was built from all uncaught events

=head3 tree (stag)

       Title: tree
     Synonym: stag

        Args: 
      Return: L<Data::Stag>
     Example: print $parser->handler->tree->xml;

returns the tree that was built from all uncaught events

=head1 CAUGHT EVENTS

A L<Data::Stag::BaseGenerator> class will generate events by calling the following methods on this class:

=over

=item start_event NODENAME

=item evbody DATA

=item end_event NODENAME {optional}

=item event NODENAME DATA

=back

These events can be nested/hierarchical

If uncaught, these events are stacked into a stag tree, which can be
written as xml or one of the other stag formats


=head1 PROTECTED METHODS - 

=head3 s_*

        Args: handler L<Data::Stag::BaseHandler>
      Return: 
     Example: 

autogenerated method - called by the parser when ever it starts a
node; * matches the node name

override this class providing the name of the node you wish to intercept

=head3 e_*

        Args: handler L<Data::Stag::BaseHandler>, node L<Data::Stag>
      Return: node L<Data::Stag>
     Example: 

autogenerated method - called by the parser when ever it ends a
node; * matches the node name

override this class providing the name of the node you wish to intercept

=head3 CONSUMES

define this in your handler class to make explicit the list of node
names that your parser consumes; this is then used if your handler is
placed in a chain

  package MyHandler;
  use base qw(Data::Stag::BaseHandler);
  sub CONSUMES {qw(person city)}
  sub e_person {....}
  sub e_city   {....}

=head3 depth

       Title: depth

        Args: 
      Return: depth int
     Example: 

depth of the nested event tree

=head3 up

       Title: up

        Args: dist int
      Return: node stag
     Example: $stag->up(-2);

when called when intercepting a node <foo>, this will look B<dist> up
the tree to find the container node

For example, if our data contains the node <blah> below:

  <blah>
    <foo>
      <a>1</b>
    </foo>
    <foo>
      <a>2</b>
    </foo>
  </blah>

    # and we have the following code:
    $h = Data::Stag->makehandler(foo=>sub {
				           my ($self, $foo) = @_;
                                           print $foo->up(1)->xml;
                                           return});

The <foo> handler will be called twice; it will print the structure of
the containing <blah> node, but the first time round, the <blah> node
will not be complete

=head3 up_to

       Title: up_to

        Args: nodename str
      Return: node stag
     Example: $stag->up_to('blah');

Similar to up(), but it will go up the container event nodes until it
finds one with the matching name

=cut

package Data::Stag::BaseHandler;

use strict;
use Exporter;
#use XML::Filter::Base;
use vars qw(@ISA @EXPORT_OK);
use base qw(Exporter);
use Carp;
use Data::Stag;

use vars qw($VERSION);
$VERSION="0.14";

sub EMITS    { () }
sub CONSUMES { () }
sub REPLACE { () }
sub SKIP { () }

sub tree {
    my $self = shift;
    $self->{_tree} = shift if @_;
    return Data::Stag::stag_nodify($self->{_tree} || []);
}
*stag = \&tree;

# deprecated
sub messages {
    my $self = shift;
    $self->{_messages} = shift if @_;
    return $self->{_messages};
}

*error_list = \&messages;

sub message {
    my $self = shift;
    push(@{$self->messages},
         shift);
}


sub new {
    my ($class, @args) = @_;
#    my $self = XML::Filter::Base::new(@_);
    my $self = {};
    bless $self, $class;
    $self->{node} = [];
    $self->{_stack} = [];
    $self->init(@args) if $self->can("init");
    $self;
}

sub errhandler {
    my $self = shift;
    if (@_) {
        $self->{errhandler} = shift;
    }
    return $self->{errhandler};
}

sub err_event {
    my $self = shift;
    if (!$self->errhandler) {
	$self->errhandler(Data::Stag->getformathandler('xml'));
	$self->errhandler->fh(\*STDERR);
	
#	my $estag = Data::Stag->new(@_);
#	eval {
#	    confess;
#	};
#	$estag->set_stacktrace($@);
#	print STDERR $estag->xml;
#        print STDERR "NO ERRHANDLER SET\n";
#	exit 1;
    }
    if (!$self->errhandler->depth) {
	$self->errhandler->start_event("error_eventset");
    }
    $self->errhandler->event(@_);
    return;
}

sub throw {
    my $self = shift;
    confess("@_");
}

sub err {
    my $self = shift;
    my $err = shift;
    if (ref($err)) {
	$self->throw("Bad error msg $err - must not by ref");
    }
    $self->err_event(message=>$err);
    return;
}

sub trap_h {
    my $self = shift;
    $self->{_trap_h} = shift if @_;
    return $self->{_trap_h};
}

sub catch_end_sub {
    my $self = shift;
    $self->{_catch_end_sub} = shift if @_;
    return $self->{_catch_end_sub};
}


sub stack {
    my $self = shift;
    $self->{_stack} = shift if @_;
    return $self->{_stack};
}
*elt_stack = \&stack;

sub in {
    my $self = shift;
    my $in = shift;
    return 1 if grep {$in eq $_} @{$self->stack};
}

sub depth {
    my $self = shift;
    return scalar(@{$self->stack});
}


sub node {
    my $self = shift;
    $self->{node} = shift if @_;
    return $self->{node};
}

sub remove_elts {
    my $self = shift;
    $self->{_remove_elts} = [@_] if @_;
    return @{$self->{_remove_elts} || []};
}
*kill_elts = \&remove_elts;

sub flatten_elts {
    my $self = shift;
    $self->{_flatten_elts} = [@_] if @_;
    return @{$self->{_flatten_elts} || []};
}

sub skip_elts {
    my $self = shift;
    $self->{_skip_elts} = [@_] if @_;
    return @{$self->{_skip_elts} || []};
}
*raise_elts = \&skip_elts;

sub rename_elts {
    my $self = shift;
    confess "experimental feature - deprecated";
    $self->{_rename_elts} = {@_} if @_;
    return %{$self->{_rename_elts} || {}};
}

sub lookup {
    my $tree = shift;
    my $k = shift;
    my @v = map {$_->[1]} grep {$_->[0] eq $k} @$tree;
    if (wantarray) {
        return @v;
    }
    $v[0];
}

sub init {
    my $self = shift;
    $self->messages([]);
    $self->{node} = [];
}

sub perlify {
    my $word = shift;
    $word =~ s/\-/_/g;
    $word =~ s/\:/_/g;
    return $word;
}

# start_event is called at the beginning of any event;
# equivalent to the event fired at the opening of any
# xml <tag> in a SAX parser

# action: checks for method of name s_EVENTNAME()
# calls it if it is present
sub start_event {
    my $self = shift;
    my $ev = shift;
    if (grep {$ev eq $_} $self->SKIP) {
        return;
    }
    my %REPLACE = $self->REPLACE;
    if (%REPLACE) {
        $ev = $REPLACE{$ev} || $ev;
    }
    my $m = 's_'.$ev;
    $m =~ tr/\-\:/_/;

    if ($self->can($m)) {
        $self->$m($ev);
    }
    elsif ($self->can("catch_start")) {
        $self->catch_start($ev);
    }
    else {
    }

    push(@{$self->{_stack}}, $ev);

    my $el = [$ev];
    push(@{$self->{node}}, $el);
    $el;
}

# deprecated
sub S {shift->start_event(@_)}


sub evbody {
    my $self = shift;
    foreach my $arg (@_) {
        if (ref($arg)) {
            $self->event(@$arg);
        }
        else {
            my $node = $self->{node};
            my $el = $node->[$#{$node}];
            confess unless $el;
	    $el->[1] = $arg;
        }
    }
    return;
}

# deprecated
sub B {shift->evbody(@_)}
sub b {shift->evbody(@_)}

sub up {
    my $self = shift;
    my $dist = shift || 1;
    my $node = $self->node->[-$dist];
    return Data::Stag::stag_nodify($node);
}

sub up_to {
    my $self = shift;
    my $n = shift || confess "must specify node name";
    my $nodes = $self->node || [];
    my ($node) = grep {$_->[0] eq $n} @$nodes;
    confess " no such node name as $n; valid names are:".
      join(", ", map {$_->[0]} @$nodes)
	unless $node;
    return Data::Stag::stag_nodify($node);
}

# end_event is called at the end of any event;
# equivalent to the event fired at the closing of any
# xml </tag> in a SAX parser

# action: checks for method of name e_EVENTNAME()
# calls it if it is present
sub end_event {
    my $self = shift;
    my $ev = shift || '';

    if (grep {$ev eq $_} $self->SKIP) {
        return;
    }
    my %REPLACE = $self->REPLACE;
    if (%REPLACE) {
        $ev = $REPLACE{$ev} || $ev;
    }

    my $stack = $self->{_stack};
    pop(@$stack);

    my $node = $self->{node};   # array of (0..$indent)
    my $topnode = pop @$node;   # node we are closing now

#    my %rename = $self->rename_elts;
#    if ($rename{$ev}) {
#        $ev = $rename{$ev};
#        $topnode->[0] = $ev;
#    }
    
    if (!ref($topnode)) {
	confess("ASSERTION ERROR: $topnode not an array");
    }
    if (scalar(@$topnode) < 2) {
        # NULLs are treated the same as
        # empty strings
        # [if we have empty tags <abcde></abcde>
        #  then no evbody will be called - we have to
        #  fill in the equivalent of a null evbody here]
#        push(@$topnode, '');
        push(@$topnode, '');
    }
    my $topnodeval = $topnode->[1];

    my @R = ($topnode);   # return

    # check for trapped events; trap_h is a hash keyed by node name
    # the value is a subroutine to be called at the end of that node
    my $trap_h = $self->{_trap_h};
    if ($trap_h) {
	my $trapped_ev = $ev;
	my @P = @$stack;
	while (!defined($trap_h->{$trapped_ev}) && scalar(@P)) {
	    my $next = pop @P;
	    $trapped_ev = "$next/$trapped_ev";
	}

	if (defined($trap_h->{$trapped_ev})) {
	    if ($trap_h->{$trapped_ev}) {
		# call anonymous subroutine supplied in hash
		@R = $trap_h->{$trapped_ev}->($self, Data::Stag::stag_nodify($topnode));
	    }
	    else {
		@R = ();
	    }
	}
    }

    my $m = 'e_'.$ev;
    $m =~ tr/\-\:/_/;

    if ($self->can($m)) {
        @R = $self->$m(Data::Stag::stag_nodify($topnode));
    }
    elsif ($self->can("catch_end")) {
        @R = $self->catch_end($ev, Data::Stag::stag_nodify($topnode));
    }
    elsif ($self->{_catch_end_sub}) {
        @R = $self->{_catch_end_sub}->($self, Data::Stag::stag_nodify($topnode));
    }
    else {
        # do nothing
    }

    if (@$node) {
	my $el = $node->[-1]; # next node up
	if (!$el->[1]) {
	    $el->[1] = [];
	}
	
	if (scalar(@R) && !$R[0]) {
	    @R = ();
	}
	push(@{$el->[1]}, @R);
    }

    $self->tree(Data::Stag::stag_nodify($topnode));
    if (!@$stack) {
        # final event; call end_stag if required
        if ($self->can("end_stag")) {
            $self->end_stag($self->tree);
        }
    }

    return @R;
}
sub E {shift->end_event(@_)}
sub e {shift->end_event(@_)}


sub popnode {
    my $self = shift;
    my $node = $self->{node};
    my $topnode = pop @$node;
    return $topnode;
}

sub event {
    my $self = shift;
    my $ev = shift;
    my $st = shift;
    $self->start_event($ev);
    if (ref($st)) {
        if (ref($st) ne "ARRAY") {confess($st)}
	foreach (@$st) { 
	    confess("$ev $st $_") unless ref($_);
	    $self->event(@$_) 
	}
    }
    else {
	$self->evbody($st);
    }
    $self->end_event($ev);
}
*ev = \&event;


sub print {
    my $self = shift;
    print "@_";
}

sub printf {
    my $self = shift;
    printf @_;
}


sub start_element {
    my ($self, $element) = @_;

    my $name = $element->{Name};
    my $atts = $element->{Attributes};

    if (!$self->{sax_stack}) {
	$self->{sax_stack} = [];
    }
    push(@{$self->{sax_stack}}, $name);
    push(@{$self->{is_nonterminal_stack}}, 0);
    if (@{$self->{is_nonterminal_stack}} > 1) {
	$self->{is_nonterminal_stack}->[-2] = 1;
    }

    # check if we need an event
    # for any preceeding pcdata
    my $str = $self->{__str};
    if (defined $str) {
	# mixed attribute text - use element '.'
        $str =~ s/^\s*//;
        $str =~ s/\s*$//;
	if ($str) {
	    $self->event(".", $str) if $str;
	}
	$self->{__str} = undef;
    }

    $self->start_event($name);
    if ($atts && %$atts) {
	# treat atts same way as SXML
	$self->start_event('@');
	foreach my $k (keys %$atts) {
	    $self->event("$k", $atts->{$k});
	    $self->{is_nonterminal_stack}->[-1] = 1;
	}
	$self->end_event('@');
    }
    return $element;
}

sub characters {
    my ($self, $characters) = @_;
    my $char = $characters->{Data};
    if (defined $char) {
        $self->{__str} = "" unless defined $self->{__str};
        $self->{__str} .= $char;
    }
    return;
}

sub end_element {
    my ($self, $element) = @_;
    my $name = $element->{Name};
    my $str = $self->{__str};
    my $parent = pop(@{$self->{sax_stack}});
    my $is_nt = pop(@{$self->{is_nonterminal_stack}});
    if (defined $str) {
        $str =~ s/^\s*//;
        $str =~ s/\s*$//;
	if ($str || $str eq '0') {
	    if ($is_nt) {
		$self->event('.' =>
			     $str);
			     
	    }
	    else {
		$self->evbody($str);
	    }
	}
    }
    $self->end_event($name);
    $self->{__str} = undef;
#    $self->{Handler}->end_element($element);
}

sub start_document {
}

sub end_document {
}


1
