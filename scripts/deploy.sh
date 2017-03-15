#!/bin/bash
echo "Starting deploy script"
if [ "$TRAVIS_PULL_REQUEST" ]; then
    echo "Pull request, not deploying.";
    exit
else
    if [ "$TRAVIS_BRANCH" = "develop" ] || [ "$TRAVIS_BRANCH" = "master" ]; then
        cd $TRAVIS_BUILD_DIR
        mkdir build
        mv * build
        tar -czf freesewing.tgz build
        export SSHPASS=$DEPLOY_PASS
        sshpass -e scp freesewing.tgz $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/$TRAVIS_BRANCH/builds
        sshpass -e ssh $DEPLOY_USER@$DEPLOY_HOST $DEPLOY_PATH/$TRAVIS_BRANCH/scripts/deploy.sh
    else
        echo "Branch is neither master nor develop, not deploying."
    fi
fi
echo "Bye"
