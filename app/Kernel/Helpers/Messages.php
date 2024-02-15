


/**
     * logWarn
     * 
     * @param array $warn
     */
    public function logWarn($warn = [])
    {
        if ($warn) {
            $session_warns = (isset($this->warnings)) ? $this->warnings : [];
            $session_warns = (isset($_SESSION) && isset($_SESSION['warns'])) ? $_SESSION['warns'] : [];
            $warn_title = (
                count($warn) > 1
                && isset($warn['title'])
                && !empty(trim($warn['title']))
            ) ? $warn['title'] : 'warn #' . strval(count($session_warns) + 1);
            $_SESSION['warns'][$warn_title] = $warn[array_key_last($warn)];
            $this->warnings = $_SESSION['warns'];
        }
    }