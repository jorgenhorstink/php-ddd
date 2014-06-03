for i in "$@"
do
case $i in
    --configuration=*)
    CONFIGURATION="${i#*=}"
    ;;

    --destination=*)
    DESTINATION="${i#*=}"
    ;;

    --include_dir=*)
    INCLUDE_DIR="${i#*=}"
    ;;

    *)
            # unknown option
    ;;
esac
done

BASE_DIR=$(dirname $0)

php $BASE_DIR/src/app.php $CONFIGURATION $INCLUDE_DIR > $DESTINATION