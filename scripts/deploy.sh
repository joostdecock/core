#!/bin/bash
mkdir build
mv * build
tar -czf freesewing.tgz build
export SSHPASS=$DEPLOY_PASS
sshpass -e scp freesewing.tgz $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/develop/builds
sshpass -e ssh $DEPLOY_USER@$DEPLOY_HOST $DEPLOY_PATH/develop/scripts/deploy.sh
