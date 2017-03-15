#!/bin/bash
echo "Starting deploy script"
if [ -z "$TRAVIS_PULL_REQUEST" ]; then
    echo "Pull request, not deploying.";
    exit
else
    if [ "$TRAVIS_BRANCH" = "develop" ] || [ "$TRAVIS_BRANCH" = "master" ]; then
        cd $TRAVIS_BUILD_DIR
        mkdir build
        cp -R index.php channels patterns src themes patterns vendor index.php config.yml build/
        tar -czf freesewing.tgz build
        export SSHPASS=$DEPLOY_PASS
        sshpass -e scp -o stricthostkeychecking=no freesewing.tgz $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/$TRAVIS_BRANCH/builds
        sshpass -e ssh -o stricthostkeychecking=no $DEPLOY_USER@$DEPLOY_HOST $DEPLOY_PATH/$TRAVIS_BRANCH/scripts/deploy.sh
    else
        echo "Branch is neither master nor develop, not deploying."
    fi
fi
echo "Bye"
