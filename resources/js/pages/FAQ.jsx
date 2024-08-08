import React from "react";
import Accordion from "react-bootstrap/Accordion";
import combinedData from "../components/TermData";

function FAQ() {
  return (
    <div className="container mt-5 mb-5 accordian">
      <h1 className="text-center">Frequently Asked Questions</h1>

      <div className="feed-container text-black mt-5">
        {combinedData.faq.map((val, index) => (
          <div key={index}>

            <Accordion>
              <Accordion.Item className="mb-4" eventKey="0">
                <Accordion.Header style={{ height: (index === 1 || index === 8) ? "72px" : "" }}>
                  <span>{val.num}</span>
                  {val.question}
                </Accordion.Header>
                <Accordion.Body>{val.answer}</Accordion.Body>
              </Accordion.Item>
            </Accordion>
            
          </div>
        ))}
      </div>
    </div>
  );
}

export default FAQ;