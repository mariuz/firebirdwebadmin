#!/bin/bash
     for fl in *.txt; do
     mv $fl $fl.old
     sed 's/ibase_/fbird_/g' $fl.old > $fl
     rm -f $fl.old
     done
