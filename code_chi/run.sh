#!/bin/sh

dir=`dirname $0`

nohup php $dir/sub/check_sub.php &
nohup php $dir/bridge/bridge.php &
nohup php $dir/send/msg_send.php &
