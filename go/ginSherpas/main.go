package main

import (
	"fmt"
	"github.com/gin-gonic/gin"
	"io/ioutil"
	"regexp"
)
func main() {
	r := gin.Default()
	r.GET("/", func(c *gin.Context) {

		bodyBytes, _ := ioutil.ReadAll(c.Request.Body)
		s := string(bodyBytes)

		reg := regexp.MustCompile(`<div>(?s:(.*?))</div>`)


		c.JSON(200, gin.H{
			"message": "pong",
		})
	})
	r.Run(":80")
}
