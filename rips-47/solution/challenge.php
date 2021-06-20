class RealSecureLoginManager {
    private $em;
    private $user;
    private $password;


    public function __construct($user, $password) {
        $this->em = DoctrineManager::getEntityManager();
        $this->user = $user;
        $this->password = $password;
    }

    public function isValid() {
        // 2) $pass depends on of user input and md5 returns binary output
        //    (not hex encoded).
        $pass = md5($this->password, true);
        $user = $this->sanitizeInput($this->user);

        $queryBuilder = $this->em->createQueryBuilder()
                            ->select("COUNT(p)")
                            ->from("user", "u")
                      // 3) $pass can escape the string when the md5 binary
                      //    output contains ' (\x27)
                      //    $user can then be used for sqli e.g. OR 1=1--
                            ->where("password = '$pass' AND user = '$user'");
        $query = $queryBuilder->getQuery();
        return boolval($query->getSingelScalarResult());
    }

    public function sanitizeInput($input) {
        return addslashes($input);
    }
}

$auth = new RealSecureLoginManager(
    // 1) $user and $password = user input
    $_POST['user'],
    $_POST['passwd']
);
if(!$auth->isValid()) {
    exit;
}

// example:
// md5_binary = abc..'
// user = OR 1=1--
// password = 'abc..'' AND user = ' OR 1=1--'
