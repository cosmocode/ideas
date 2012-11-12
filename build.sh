#!/bin/sh

# Creates a tar ball and leaves out some stuff not needed for a live
# setup.

gitexcludes='';
for X in `cat .gitignore`
do
    gitexcludes="$gitexcludes --exclude=$X";
done

tar -czvf ideas.tgz -C .. \
    --exclude=.git \
    --exclude=.gitignore \
    --exclude=.gitmodules \
    --exclude=third_party/lessphp/tests \
    --exclude=third_party/Twig/test \
    --exclude=third_party/Twig/doc \
    --exclude=sql/database.mwb \
    $gitexcludes \
    ideas
