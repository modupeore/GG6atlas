#!/usr/bin/perl 
use Pod::Usage;
use Getopt::Long;
use DBI; 
use threads;
use Thread::Queue;
use lib '/home/modupe/SCRIPTS/SUB';
use passw;
use routine;

chdir "/home/modupe/public_html/GG6atlas/OUTPUT";
$fastbit="/home/modupe/public_html/newTADtransatlasFB";
$ibis="/home/modupe/.bin/bin/ibis";
%ARRAYQUERY={}; undef %ARRAYQUERY; my @genearray; undef @genearray;
my $tmpname = rand(20);
my (%FPKM, %CHROM, %POSITION, %VAR) = {}; undef %VAR; undef %FPKM; undef %CHROM; undef %POSITION;
processArguments();


if ($querynosql) {
  my $newquery;
  if ($query =~ /\swhere\s/) { $query =~ /select (.+) where/; $newquery = $1; }
  else { $query =~ /select (.+)/; $newquery = $1;}
  $newquery =~ s/\s//g; $newquery = uc($newquery);
  @header = split("\,",$newquery);
  $querynosql = $fastbit."/".$querynosql;
  `$ibis -d $querynosql -q '$query' -o $nosql 2>>$efile`;

  my $found = `head -n 1 $nosql`;
  if (length($found) > 1) {
    open(IN,"<",$nosql);
      while (<IN>){
        chomp;
        my @row = undef;
        my @all = split (/\, /, $_);
        foreach (@all) {
          $_ =~ s/^'|'$|^"|"$//g; #removing the quotation marks from the words 
          push @row, $_;
        } shift (@row);
        $count++; $ARRAYQUERY{$count} = [@row];
      } close (IN);


      open (OUT, ">$output") or die "ERROR:\t Output file $output can be not be created\n";
      print OUT join("\t", @header),"\n";
      foreach my $row (sort {$a <=> $b} keys %ARRAYQUERY) {
        no warnings 'uninitialized';
        print OUT join("\t", @{$ARRAYQUERY{$row}}),"\n";
     } #end foreach printout
     close OUT;
     `rm -rf $nosql $efile`;
  } #end if found 
} # end of nosql query module


if ($query && !$querynosql) {
  my $dbh= mysql();
  $query=~s/^\s+|\s+$//g;
  $sth = $dbh->prepare($query); $sth->execute() or die "i didnt work";
  @header = @{ $sth->{NAME_uc} }; 
  while (my @row = $sth->fetchrow_array()) {
    $count++; $ARRAYQUERY{$count} = [@row];
  }
  open (OUT, ">", $output);
  print OUT join("\t", @header),"\n";
  foreach my $row (sort {$a <=> $b} keys %ARRAYQUERY) {
    print OUT join("\t", @{$ARRAYQUERY{$row}}),"\n";
  } close OUT;
} # end of sql query module


if ($genexp){ #looking at gene expression per sample
  $count = 0;
  #making sure required attributes are specified.
  my $gfastbit = $fastbit."/gene-information";
  #checking if the organism is in the database
  $organism =~ s/^\s+|\s+$//g;
  my @sample = split(",", $sample); undef $sample; 
  foreach (@sample) {
    $_ =~ s/^\s+|\s+$//g;
    $sample .= $_ .",";
  }chop $sample;
  my $snumber= 0;
  @headers = split(",", $sample);
  if ($extpm){
    $syntax = "select genename, geneid, tpm, libraryid, chrom, start, stop where tpm != 0 and";
  } else {
    $syntax = "select genename, geneid, fpkm, libraryid, chrom, start, stop where fpkm != 0 and";
  }
  if ($gene) {
    my @genes = split(",", $gene); undef $gene;
    foreach (@genes){
      $_ =~ s/^\s+|\s+$//g;
      $gene .= $_.",";
    } chop $gene;
  }
        
  foreach my $header (@headers){ 
    my $newsyntax;

    if ($gene) {
      my @genes = split(",", $gene);
      foreach (@genes){
        $_ =~ s/^\s+|\s+$//g;
        $newsyntax = $syntax." genename like '%$_%' and libraryid = '$header' ORDER BY geneid desc;";
        `$ibis -d $gfastbit -q "$newsyntax" -o $nosql 2>>$efile`;
                    
        open(IN,"<",$nosql);
        while (<IN>){
          chomp;
          my ($genename, $geneid, $fpkm, $library, $chrom, $start, $stop) = split /\, /; 
          $geneid =~ s/^'|'$|^"|"$//g; $genename =~ s/^'|'$|^"|"$//g; $library =~ s/^'|'$|^"|"$//g; $chrom =~ s/^'|'$|^"|"$//g; #removing quotation marks if applicable
          $FPKM{"$geneid|$chrom"}{$library} = $fpkm;
          $CHROM{"$geneid|$chrom"} = $chrom;
          $POSITION{"$geneid|$chrom"}{$library} = "$start|$stop";
          $GENEID{"$geneid|$chrom"} = $genename;

        } close (IN); `rm -rf $nosql`;
      } # end foreach gene
    }
    elsif ($ensembl) {
      my @ensembl = split(",", $ensembl);
      foreach (@ensembl) {
        $_ =~ s/^\s+|\s+$//g;
        $newsyntax = $syntax." geneid like '%$_%' and libraryid = '$header' ORDER BY geneid desc;";
        `$ibis -d $gfastbit -q "$newsyntax" -o $nosql 2>>$efile`;
 
        open(IN,"<",$nosql);
        while (<IN>){
          chomp;
          my ($genename, $geneid, $fpkm, $library, $chrom, $start, $stop) = split /\, /;
          $geneid =~ s/^'|'$|^"|"$//g; $genename =~ s/^'|'$|^"|"$//g; $library =~ s/^'|'$|^"|"$//g; $chrom =~ s/^'|'$|^"|"$//g; #removing quotation marks if applicable
 
          $FPKM{"$geneid|$chrom"}{$library} = $fpkm;
          $CHROM{"$geneid|$chrom"} = $chrom;
          $POSITION{"$geneid|$chrom"}{$library} = "$start|$stop";
          $GENEID{"$geneid|$chrom"} = $genename;
 
        } close (IN); `rm -rf $nosql`;
      } # end foreach ensembl
    } 
    else {
      $newsyntax = $syntax." libraryid = '$header' ORDER BY geneid desc;";
      `$ibis -d $gfastbit -q "$newsyntax" -o $nosql 2>>$efile`;

      open(IN,"<",$nosql);
      while (<IN>){ 
        chomp;
        my ($genename, $geneid, $fpkm, $library, $chrom, $start, $stop) = split /\, /; 
        $geneid =~ s/^'|'$|^"|"$//g; $genename =~ s/^'|'$|^"|"$//g; $library =~ s/^'|'$|^"|"$//g; $chrom =~ s/^'|'$|^"|"$//g; #removing quotation marks if applicable
        $FPKM{"$geneid|$chrom"}{$library} = $fpkm;
        $CHROM{"$geneid|$chrom"} = $chrom;
        $POSITION{"$geneid|$chrom"}{$library} = "$start|$stop";
        $GENEID{"$geneid|$chrom"} = $genename;
      } close (IN); `rm -rf $nosql`;
    }
            
  } #end foreach extracting information from the database    
  foreach my $newgene (sort keys %CHROM){ #turning the genes into an array
    if ($newgene =~ /^[\d\w]/){ push @genearray, $newgene;}
  }
  push @VAR, [ splice @genearray, 0, 2000 ] while @genearray; #sub array the genes into a list of 2000
  @headers = split(",", $sample);
  foreach (0..$#VAR){ $newfile .= "tmp_".$tmpname."-".$_.".zzz "; } #foreach sub array create a temporary file
  $queue = new Thread::Queue();
  my $builder=threads->create(\&main); #create thread for each subarray into a thread
  push @threads, threads->create(\&processor) for 1..5; #execute 5 threads
  $builder->join; #join threads
  foreach (@threads){$_->join;}
  my $command="cat $newfile >> $tmpout"; #path into temporary output
  system($command);
  @header = qw|GENEID GENENAME CHROM|; push @header, @headers;
  $count = `cat $tmpout | wc -l`; chomp $count;
  open my $content,"<",$tmpout; `rm -rf $tmpout`;
  unless ($count == 0) {
    open (OUT, ">$output") or die "ERROR:\t Output file $output can be not be created\n";
    print OUT join("\t", @header),"\n";
    print OUT <$content>;
    close OUT;
  }
  `rm -rf $newfile $efile`;
} #end of genexp module


if ($avgexp){ #looking at average fpkms
  my $gfastbit = $fastbit."/gene-information";
  $count = 0;
  undef %ARRAYQUERY;

  $organism =~ s/^\s+|\s+$//g;
        
  if ($tissue) {
    my @tissue = split(",", $tissue); undef $tissue; 
    foreach (@tissue) {
      $_ =~ s/^\s+|\s+$//g;
      $tissue .= $_ .",";
    } chop $tissue;
   } else {
     $sth = $dbh->prepare("select tissue from vw_sampleinfo where organism = '$organism' and genes is not null"); #get samples
     $sth->execute or die "SQL Error: $DBI::errstr\n";
     my $tnumber= 0;
     while (my $row = $sth->fetchrow_array() ) {
       $tnumber++;
       $SAMPLE{$tnumber} = $row;
       $tissue .= $row.",";
     } chop $tissue;
   } #checking sample options

   my @tissue = split(",", $tissue);
   if ($gene) { @genes = split(",", $gene); $newcrit = "genename"; } elsif ($ensembl) { @genes = split(",",$ensembl); $newcrit = "geneid";}
#   my @genes = split(",", $gene);
   foreach my $fgene (@genes){
     $fgene =~ s/^\s+|\s+$//g;
     foreach my $ftissue (@tissue) {
       if ($extpm) {
         @header = ("GENENAME","GENEID", "TISSUE", "MAXIMUM TPM", "AVERAGE TPM", "MINIMUM TPM");
         `$ibis -d $gfastbit -q 'select genename, geneid, max(fpkm), avg(fpkm), min(fpkm), max(tpm), avg(tpm), min(tpm) where $newcrit like "%$fgene%" and tissue = "$ftissue" and organism = "$organism"  and tpm != 0' -o $nosql 2>>$efile`;
       } else {
         @header = ("GENENAME","GENEID", "TISSUE", "MAXIMUM FPKM", "AVERAGE FPKM", "MINIMUM FPKM");
         `$ibis -d $gfastbit -q 'select genename, geneid, max(fpkm), avg(fpkm), min(fpkm), max(tpm), avg(tpm), min(tpm) where $newcrit like "%$fgene%" and tissue = "$ftissue" and organism = "$organism" and fpkm != 0' -o $nosql 2>>$efile`;
       }
       my $found = `head -n 1 $nosql`;
       if (length($found) > 1) {
         open(IN,"<",$nosql);
         while (<IN>){
           chomp;
           my ($genename,$idgene, $fmax,$favg,$fmin,$tmax,$tavg,$tmin) = split (/\, /, $_, 8);
           $genename =~ s/^'|'$|^"|"$//g; $idgene =~ s/^'|'$|^"|"$//g; #removing the quotation marks from the words
           if ($extpm) {
             @row = ($genename,$idgene, $ftissue, $tmax, $tavg, $tmin);
           } else {
             @row = ($genename,$idgene, $ftissue, $fmax, $favg, $fmin);
           }
           $count++;
           $ARRAYQUERY{$genename}{$idgene}{$ftissue} = [@row];
         } close (IN); `rm -rf $nosql`;
       } 
     }
   }
   unless ($count == 0) {
     open (OUT, ">$output") or die "ERROR:\t Output file $output can be not be created\n";
     print OUT join("\t", @header),"\n";
     foreach my $a (sort keys %ARRAYQUERY){
       foreach my $b (sort keys % { $ARRAYQUERY{$a} }){
         foreach my $c (sort keys % { $ARRAYQUERY{$a}{$b} }){
           print OUT join("\t", @{$ARRAYQUERY{$a}{$b}{$c}}),"\n";
         }
       }
     } close OUT;
   } 
   `rm -rf $nosql $efile`;
}  # end of avgexp module


sub processArguments {
    my @commandline = @ARGV;
    GetOptions('verbose|v'=>\$verbose, 'help|h'=>\$help, 'man|m'=>\$man, 'query=s'=>\$query, 'nosql=s'=>\$querynosql, 'db2data'=>\$dbdata, 'o|output=s'=>\$output,'w'=>\$log,
                         'avgexp'=>\$avgexp, 'gene=s'=>\$gene, 'tissue=s'=>\$tissue, 'species=s'=>\$organism, 'genexp'=>\$genexp, 'fpkm'=>\$exfpkm,
                         'tpm'=>\$extpm, 'vcf'=>\$vcf, 'samples|sample=s'=>\$sample, 'chrvar'=>\$chrvar, 'chromosome=s'=>\$chromosome,
                         'varanno'=>\$varanno,'region=s'=>\$region, 'ensembl=s'=>\$ensembl) or pod2usage ();

$tmpout=$output."-tmpout";
$nosql=$output."-nosql";
$efile=$output."-efile";

}
sub main {
    no warnings;
    foreach my $count (0..$#VAR) {
        my $namefile = "tmp_".$tmpname."-".$count.".zzz";
        push $VAR[$count], $namefile;
        while(1) {
            if ($queue->pending() <100) {
                $queue->enqueue($VAR[$count]);
                last;
            }
        }
    }
    foreach(1..5) { $queue-> enqueue(undef); }
}

sub processor {
    my $query;
    while ($query = $queue->dequeue()){
        collectsort(@$query);
    }
}

sub sortposition {
  my $genename = $_[0];
  my $status = "nothing";
    my @newstartarray; my @newstoparray;
    foreach my $libest (sort keys % {$POSITION{$genename}} ) {
        my ($astart, $astop, $status) = VERDICT(split('\|',$POSITION{$genename}{$libest},2));
    push @newstartarray, $astart;
        push @newstoparray, $astop;
        if ($status eq "reverse"){
            $realstart = (sort {$b <=> $a} @newstartarray)[0];
            $realstop = (sort {$a <=> $b} @newstoparray)[0];
        } else {
            $realstart = (sort {$a <=> $b} @newstartarray)[0];
            $realstop = (sort {$b <=> $a} @newstoparray)[0];    
        }
        $REALPOST{$genename} = "$realstart|$realstop"; 
    }
}

sub VERDICT {
    my (@array) = @_;
    my $status = "nothing";
    my (@newstartarray, @newstoparray);
    if ($array[0] > $array[1]) {
        $status = "reverse";
    }
    elsif ($array[0] < $array[1]) {
        $status = "forward";
    }
    return $array[0], $array[1], $status;
}

sub collectsort{
    my $file = pop @_;
    open(OUT2, ">$file");
    foreach (@_){    
        sortposition($_);
    }
    foreach my $genename (sort @_){ 
        if ($genename =~ /^\S/){
            my ($realstart,$realstop) = split('\|',$REALPOST{$genename},2);
            my $realgenes = (split('\|',$genename))[0];
            print OUT2 $realgenes,"\t";
            if ($GENEID{$genename} =~ /NULL$/) { print OUT2 "\t"; }
            else { print OUT2 $GENEID{$genename}."\t"; }
            if ($CHROM{$genename} =~ /NULL$/) { print OUT2 "\t"; }
            else { print OUT2 $CHROM{$genename}."\:".$realstart."\-".$realstop."\t"; }
            foreach my $lib (0..$#headers-1){
                if (exists $FPKM{$genename}{$headers[$lib]}){
                    print OUT2 "$FPKM{$genename}{$headers[$lib]}\t";
                }
                else {
                    print OUT2 "0\t";
                }
            }
            if (exists $FPKM{$genename}{$headers[$#headers]}){
                print OUT2 "$FPKM{$genename}{$headers[$#headers]}\n";
            }
            else {
                print OUT2 "0\n";
            }
        }
  }close (OUT2)
}
