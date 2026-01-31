#!/bin/bash
tmux new-session -s phpFrameworkDev -d 
tmux send-keys -t phpFrameworkDev:0 "nvim" C-m
tmux new-window -t phpFrameworkDev:1 -n "tests"
tmux send-keys -t phpFrameworkDev:1 "while :; do clear; ./vendor/bin/phpunit; sleep 1; done" C-m
tmux new-window -t phpFrameworkDev:2 -n "git"
tmux send-keys -t phpFrameworkDev:2 "git log && git diff && clear && git status && git stash list && read -s && tmux select-window -t phpFrameworkDev:0 && exit" C-m
tmux select-window -t phpFrameworkDev:2
tmux attach-session -t phpFrameworkDev
