package my

import "github.com/streadway/amqp"

type Rmq struct{

}

func NewRmq() *Rmq{
	return new(Rmq)
}

func (this *Rmq) Index(){

	name := "job_queue"
	addr := "amqp://guest:guest@localhost:5672/"
	conn, err := amqp.Dial(addr)

	if(err != nil){
		panic(err)
	}

	conn.NotifyClose(make(chan *amqp.Error))
	ch, err := conn.Channel()
	if err != nil {
		panic(err)
	}

	err = ch.Confirm(false)

	if err != nil {
		panic(err)
	}
	_, err = ch.QueueDeclare(
		name,
		false, // Durable
		false, // Delete when unused
		false, // Exclusive
		false, // No-wait
		nil,   // Arguments
	)

	ch.NotifyPublish(make(chan amqp.Confirmation, 1))

	message := []byte("message")

	err = ch.Publish(
		"",           // Exchange
		name, // Routing key
		false,        // Mandatory
		false,        // Immediate
		amqp.Publishing{
			ContentType: "text/plain",
			Body:        message,
		})

	if(err != nil){
		panic(err)
	}

	err = ch.Close()
	if(err != nil){
		panic(err)
	}

}

func (this *Rmq) connect(){


}