package my

import (
	"bytes"
	"fmt"
	"os/exec"
)

type Tcommand struct {

}

func(this *Tcommand) Index(){

	cmd := exec.Command("cmd","ps")

	var stdout bytes.Buffer
	var stderr bytes.Buffer
	cmd.Stdout = &stdout
	cmd.Stderr = &stderr
	err := cmd.Run()

	fmt.Println(err)
	fmt.Println(stdout.String())
	fmt.Println(stderr.String())

	fmt.Println("xxx")

}